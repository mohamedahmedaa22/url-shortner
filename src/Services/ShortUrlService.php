<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Models\ShortUrl;
use App\Repositories\ShortUrlRepository;
use RuntimeException;
use PDOException;

class ShortUrlService
{
    private const CODE_LENGTH = 6;
    private const MAX_ATTEMPTS = 5;
    private const MAX_URL_LENGTH = 2048;
    private const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    public function __construct(private ShortUrlRepository $repository) {}

    public function create(mixed $url): ShortUrl
    {
        $url = $this->validateUrl($url);

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $code = $this->generateCode();

            if ($this->repository->codeExists($code)) {
                continue;
            }

            try {
                return $this->repository->create($url, $code);
            } catch (PDOException $e) {
                if (($e->errorInfo[1] ?? null) === 1062) {
                    continue;
                }
                throw $e;
            }
        }

        throw new RuntimeException('Could not generate a unique short code.');
    }

    public function resolve(string $code): ShortUrl
    {
        $shortUrl = $this->find($code);
        $this->repository->incrementAccessCount($code);
        return $shortUrl;
    }

    public function update(string $code, mixed $url): ShortUrl
    {
        $url = $this->validateUrl($url);

        return $this->repository->updateUrl($code, $url)
            ?? throw new NotFoundException();
    }

    public function delete(string $code): void
    {
        if (! $this->repository->delete($code)) {
            throw new NotFoundException();
        }
    }

    public function stats(string $code): ShortUrl
    {
        return $this->find($code);
    }

    private function find(string $code): ShortUrl
    {
        return $this->repository->findByCode($code)
             ?? throw new NotFoundException();
    }

    private function validateUrl(mixed $url): string
    {
        if (!is_string($url) || trim($url) === '') {
            throw new ValidationException(['url' => ['The url field is required.']]);
        }

        $url = trim($url);

        if (strlen($url) > self::MAX_URL_LENGTH) {
            throw new ValidationException(['url' => ['The url may not be greater than 2048 characters.']]);
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (filter_var($url, FILTER_VALIDATE_URL) === false || !in_array($scheme, ['http', 'https'], true)) {
            throw new ValidationException(['url' => ['The url must be a valid http or https URL.']]);
        }

        return $url;
    }

    private function generateCode(): string
    {
        $max = strlen(self::ALPHABET) - 1;
        $code = '';

        for ($i = 0; $i < self::CODE_LENGTH; $i++) {
            $code .= self::ALPHABET[random_int(0, $max)];
        }

        return $code;
    }
}
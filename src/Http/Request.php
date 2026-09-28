<?php

namespace App\Http;

class Request
{
    public function __construct(
        private string $method,
        private string $path,
        private array $body
    ){}

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $body = json_decode(file_get_contents('php://input'), true);

        return new self($method, $path, is_array($body) ? $body : []);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }
}
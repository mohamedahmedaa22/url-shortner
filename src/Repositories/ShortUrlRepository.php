<?php

namespace App\Repositories;

use App\Models\ShortUrl;
use PDO;

class ShortUrlRepository
{
    public function __construct(private PDO $db) {}

    public function create(string $url, string $shortCode): ShortUrl
    {
        $now = gmdate('Y-m-d H:i:s');

        $stmt = $this->db->prepare(
            'INSERT INTO short_urls (url, short_code, created_at, updated_at) VALUES (:url, :short_code, :created_at, :updated_at)'
        );

        $stmt->execute([
            'url' => $url,
            'short_code' => $shortCode,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->findByCode($shortCode);
    }

    public function findByCode(string $shortCode): ?ShortUrl
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM short_urls WHERE short_code = :short_code'
        );

        $stmt->execute(['short_code' => $shortCode]);

        $row = $stmt->fetch();

        return $row ? ShortUrl::fromRow($row) : null;
    }

    public function codeExists(string $shortCode): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM short_urls WHERE short_code = :short_code LIMIT 1'
        );

        $stmt->execute(['short_code' => $shortCode]);

        return (bool) $stmt->fetchColumn();
    }

    public function updateUrl(string $shortCode, string $newUrl): ?ShortUrl
    {
        $stmt = $this->db->prepare(
            'UPDATE short_urls SET url = :url, updated_at = :updated_at WHERE short_code = :short_code LIMIT 1'
        );

        $stmt->execute([
            'url' => $newUrl,
            'short_code' => $shortCode,
            'updated_at' => gmdate('Y-m-d H:i:s'),
        ]);

        return $this->findByCode($shortCode);
    }

    public function delete(string $shortCode): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM short_urls WHERE short_code = :short_code'
        );
        $stmt->execute(['short_code' => $shortCode]);

        return $stmt->rowCount() > 0;
    }

    public function incrementAccessCount(string $shortCode): void
    {
        $stmt = $this->db->prepare(
            'UPDATE short_urls
             SET access_count = access_count + 1
             WHERE short_code = :short_code'
        );
        $stmt->execute(['short_code' => $shortCode]);
    }
}
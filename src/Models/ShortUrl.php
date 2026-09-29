<?php

namespace App\Models;

use DateTimeImmutable;
use DateTimeZone;

class ShortUrl
{
    public function __construct(
        public readonly int $id,
        public string $url,
        public readonly string $shortCode,
        public int $accessCount,
        public readonly DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {}

    public static function fromRow(array $row): self
    {
        $utc = new DateTimeZone('UTC');

        return new self(
            id: (int) $row['id'],
            url: $row['url'],
            shortCode: $row['short_code'],
            accessCount: (int) $row['access_count'],
            createdAt: new DateTimeImmutable($row['created_at'], $utc),
            updatedAt: new DateTimeImmutable($row['updated_at'], $utc),
        );
    }

    public function toArray(bool $withStats = false): array
    {
        $data = [
            'id' => (string) $this->id,
            'url' => $this->url,
            'shortCode' => $this->shortCode,
            'createdAt' => $this->createdAt->format('Y-m-d\TH:i:s\Z'),
            'updatedAt' => $this->updatedAt->format('Y-m-d\TH:i:s\Z'),
        ];

        if ($withStats) {
            $data['accessCount'] = (int) $this->accessCount;
        }

        return $data;
    }
}
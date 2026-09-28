<?php

namespace App\Http;

class JsonResponse
{
    public function __construct(
        private mixed $data = null,
        private int $status = 200
    ) {}

    public function send(): void
    {
        http_response_code($this->status);

        if ($this->status === 204) {
            return;
        }

        header('Content-Type: application/json');
        echo json_encode($this->data, JSON_UNESCAPED_SLASHES);
    }
}
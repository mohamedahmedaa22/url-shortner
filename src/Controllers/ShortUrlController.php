<?php

namespace App\Controllers;

use App\Http\JsonResponse;
use App\Http\Request;
use App\Services\ShortUrlService;

class ShortUrlController
{
    public function __construct(private ShortUrlService $service) {}

    public function store(Request $request): JsonResponse
    {
        $shortUrl = $this->service->create($request->input('url'));

        return new JsonResponse($shortUrl->toArray(), 201);
    }

    public function show(Request $request, string $code): JsonResponse
    {
        return new JsonResponse($this->service->resolve($code)->toArray());
    }

    public function update(Request $request, string $code): JsonResponse
    {
        $shortUrl = $this->service->update($code, $request->input('url'));

        return new JsonResponse($shortUrl->toArray());
    }

    public function destroy(Request $request, string $code): JsonResponse
    {
        $this->service->delete($code);

        return new JsonResponse(null, 204);
    }

    public function stats(Request $request, string $code): JsonResponse
    {
        return new JsonResponse($this->service->stats($code)->toArray(withStats: true));
    }
}
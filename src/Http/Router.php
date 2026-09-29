<?php

namespace App\Http;

class Router
{
    private array $routers = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);

        $this->routers[] = [
            'method' => $method,
            'regex'   => '#^' . $regex . '$#',
            'handler' => $handler
        ];
    }

    public function dispatch(Request $request): JsonResponse
    {
        foreach ($this->routers as $route) {

            if ($route['method'] !== $request->method()) {
                continue;
            }

            if (preg_match($route['regex'], $request->path(), $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return ($route['handler'])($request, ...$params);   // ← add $request
            }
        }

         return new JsonResponse(['error' => 'Not Found'], 404);
    }
}
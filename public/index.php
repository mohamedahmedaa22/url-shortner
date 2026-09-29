<?php

use App\Controllers\ShortUrlController;
use App\Database;
use App\Http\JsonResponse;
use App\Http\Router;
use App\Repositories\ShortUrlRepository;
use App\Services\ShortUrlService;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    $controller = new ShortUrlController(
        new ShortUrlService(
            new ShortUrlRepository(Database::connect())
        )
    );

    $router = new Router();

    $router->add('POST', '/shorten', [$controller, 'store']);
    $router->add('GET', '/shorten/{code}', [$controller, 'show']);
    $router->add('PUT', '/shorten/{code}', [$controller, 'update']);
    $router->add('DELETE', '/shorten/{code}', [$controller, 'destroy']);
    $router->add('GET', '/shorten/{code}/stats', [$controller, 'stats']);

    $response = $router->dispatch(\App\Http\Request::fromGlobals());
} catch(\App\Exceptions\ValidationException $exception) {
    $response = new JsonResponse([
        'message' => $exception->getMessage(),
        'errors' => $exception->errors()
    ], 400);
} catch(\App\Exceptions\NotFoundException $exception) {
    $response = new JsonResponse(['error' => $exception->getMessage()], 404);
} catch (Throwable $exception) {
    error_log((string) $exception);
    $response = new JsonResponse(['error' => "Internal Server Error"], 500);
}

$response->send();
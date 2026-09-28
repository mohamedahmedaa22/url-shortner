<?php

use App\Http\JsonResponse;
use App\Http\Router;
use App\Http\Request;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$router = new Router();

$router->add('GET', '/shorten/{code}', fn (string $code) => new JsonResponse(['code' => $code]));
$router->add('GET', '/shorten/{code}/stats', fn (string $code) => new JsonResponse(['stats for' => $code]));

$router->dispatch(Request::fromGlobals())->send();


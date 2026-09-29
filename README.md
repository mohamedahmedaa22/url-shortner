# URL Shortening Service

A RESTful API for shortening long URLs, built in plain PHP (no framework) with MySQL.

Project idea from roadmap.sh: https://roadmap.sh/projects/url-shortening-service

## Features

- Create a short URL with a unique, randomly generated code
- Retrieve the original URL from a short code
- Update the URL behind an existing short code
- Delete a short URL
- Track and retrieve access statistics

## Requirements

- PHP 8.3+ with `pdo_mysql`
- MySQL
- Composer

## Setup

```bash
composer install
cp .env.example .env        # then set your database credentials
mysql -u root -p -e "CREATE DATABASE url_shortner"
mysql -u root -p url_shortner < database/scheme.sql
php -S localhost:8000 -t public
```

## API

| Method | Endpoint | Description | Success | Errors |
|---|---|---|---|---|
| `POST` | `/shorten` | Create a short URL | `201 Created` | `400` |
| `GET` | `/shorten/{code}` | Retrieve the original URL | `200 OK` | `404` |
| `PUT` | `/shorten/{code}` | Update the URL | `200 OK` | `400`, `404` |
| `DELETE` | `/shorten/{code}` | Delete the short URL | `204 No Content` | `404` |
| `GET` | `/shorten/{code}/stats` | Get access statistics | `200 OK` | `404` |

### Create a short URL

```bash
curl -X POST localhost:8000/shorten \
  -H 'Content-Type: application/json' \
  -d '{"url": "https://www.example.com/some/long/url"}'
```

```json
{
  "id": "1",
  "url": "https://www.example.com/some/long/url",
  "shortCode": "abc123",
  "createdAt": "2021-09-01T12:00:00Z",
  "updatedAt": "2021-09-01T12:00:00Z"
}
```

### Get statistics

```bash
curl localhost:8000/shorten/abc123/stats
```

```json
{
  "id": "1",
  "url": "https://www.example.com/some/long/url",
  "shortCode": "abc123",
  "createdAt": "2021-09-01T12:00:00Z",
  "updatedAt": "2021-09-01T12:00:00Z",
  "accessCount": 10
}
```

### Validation errors

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "url": ["The url must be a valid http or https URL."]
  }
}
```

## Running tests

```bash
composer test
```

## Project structure

```
public/index.php        Entry point, routes and error handling
src/Http/               Request, Router, JsonResponse
src/Controllers/        HTTP layer
src/Services/           Business logic and validation
src/Repositories/       Database access (PDO)
src/Models/             ShortUrl model
src/Exceptions/         NotFoundException, ValidationException
database/scheme.sql     Database schema
tests/Unit/             PHPUnit tests
```

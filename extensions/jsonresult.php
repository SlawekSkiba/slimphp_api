<?php

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;

class JsonResponse
{
    public static function withJson(Response $response, mixed $content)
    {
        $response->getBody()->write(
            json_encode($content)
        );

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    }

    public static function unauthorized(Response $response, string $message = null)
    {
        $response->getBody()->write(
            json_encode(['message' => $message ?: 'Unauthorized'])
        );

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(401);
    }

    public static function created(Response $response, string $url, mixed $body = null)
    {
        if ($body != null) {
            $response->getBody()->write(
                json_encode($body)
            );
        }

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader("Location", $url)
            ->withStatus(201);
    }

    public static function deleted(Response $response)
    {
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(204);
    }

    public static function notFound(Response $response, string $message = "Not found")
    {
        $response->getBody()->write(
            json_encode(array('error' => $message))
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(404)(array('error' => $message));
    }

    public static function error(Response $response, string $message = "Not found")
    {
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500, $message);
    }

    public static function badRequest(Response $response, string $message = "Error occured")
    {

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400, $message);
    }

    public static function conflict(Response $response, string $message = "Already exists")
    {
        $result["error"] = $message;

        $response->getBody()->write(
            json_encode($result)
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(409, $message);
    }
}

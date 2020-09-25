<?php

namespace App;

use React\Http\Message\Response;

final class JsonResponse
{
    private static function response(int $statusCode, $data = null): Response
    {
        $body = $data ? json_encode($data) : null;

        return new Response($statusCode, ['Content-Type' => 'application/json'], $body);
    }

    public static function ok($data = null): Response
    {
        return self::response(200, $data);
    }

    public static function noContent(): Response
    {
        return self::response(204);
    }

    public static function created(): Response
    {
        return self::response(201);
    }

    public static function badRequest(string $error): Response
    {
        return self::response(400, ['error' => $error]);
    }

    public static function notFound(string $error): Response
    {
        return self::response(404, ['error' => $error]);
    }
}

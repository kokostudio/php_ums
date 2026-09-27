<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\RateLimiter;
use App\Core\Session;

class RateLimitMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $endpoint = rtrim($uri, '/');
        if (empty($endpoint)) {
            $endpoint = '/';
        }

        if (!RateLimiter::check($endpoint)) {
            // If it's an API request, return JSON
            if (strpos($endpoint, '/api') === 0) {
                http_response_code(429);
                echo json_encode(['error' => 'Too Many Requests']);
                exit;
            }

            // For web requests, set flash and redirect back (or throw exception)
            // Throwing exception 429 so ErrorHandler catches it is cleaner
            throw new \Exception("Too Many Requests. Please try again later in 15 minutes.", 429);
        }

        // Increment attempt
        RateLimiter::increment($endpoint);

        return true;
    }
}

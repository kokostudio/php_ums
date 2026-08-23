<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Core\Settings;

class JwtMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $this->unauthorized('Token not found in request');
        }

        $jwt = $matches[1];
        
        // We will store the secret in env or settings
        $secret = $_ENV['JWT_SECRET'] ?? Settings::get('jwt_secret');

        if (!$secret) {
            $this->unauthorized('JWT Secret not configured in server');
        }

        try {
            $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));
            
            // Store user data in global request or a simple static property for controllers to use
            $_REQUEST['jwt_user'] = (array) $decoded;
            return true;

        } catch (\Exception $e) {
            $this->unauthorized('Invalid or expired token: ' . $e->getMessage());
        }
    }

    private function unauthorized($message)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized', 'message' => $message]);
        exit;
    }
}

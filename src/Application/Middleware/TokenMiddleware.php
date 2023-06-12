<?php

namespace App\Application\Middleware;

use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;

class TokenMiddleware implements Middleware
{

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $token = $this->getTokenFromHeader($request);

        if (!$this->isTokenValid($token)) {
            $response = new \Slim\Psr7\Response();
            $response = $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response;
        } else {
            $user = $this->isTokenValid($token);
            $request = $request->withAttribute('user', $user);
        }

        // Token is valid, continue to the next middleware or route handler
        return $handler->handle($request);
    }

    private function getTokenFromHeader(Request $request): ?string
    {
        // Retrieve the token from the Authorization header
        $authorizationHeader = $request->getHeaderLine('Authorization');
        $headerParts = explode(' ', $authorizationHeader);
        $token = isset($headerParts[1]) ? $headerParts[1] : '';
        return $token;
    }

    private function isTokenValid(string $token)
    {
        $dbPath = __DIR__ . '/../../../database.db';
        $pdo = new PDO("sqlite:$dbPath");
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('SELECT * FROM users WHERE token = ?');
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        return $user;
    }
}
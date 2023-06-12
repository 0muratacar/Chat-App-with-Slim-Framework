<?php

namespace App\Models;

use App\Database;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\Psr17\ResponseFactory as ResponseFactory;
use Slim\Psr7\Stream;
use Slim\Psr7\Factory\StreamFactory;



class User
{

    public static function create($name, Request $request, Response $response)
    {
        $responseFactory = new \Slim\Psr7\Factory\ResponseFactory();
        $streamFactory = new StreamFactory();

        $pdo = Database\Database::getInstance();

        // Check does user exist
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$name]);
        $user = $stmt->fetch();

        if ($user) {
            $response = $responseFactory->createResponse(400)->withHeader('Content-Type', 'application/json')
                ->withBody($streamFactory->createStream(json_encode(['message' => 'User already exist'])));
            return $response;
        }


        $token = uniqid();

        // Add user to database.
        $stmt = $pdo->prepare('INSERT INTO users (username, token) VALUES (?, ?)');
        $stmt->execute([$name, $token]);


        $response = $responseFactory->createResponse(201)->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode(['message' => 'User created successfully'])));
        return $response;
    }

    public static function getAll(Response $response)
    {
        $responseFactory = new \Slim\Psr7\Factory\ResponseFactory();
        $streamFactory = new StreamFactory();

        $pdo = Database\Database::getInstance();

        $stmt = $pdo->query('SELECT * FROM users');
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = $responseFactory->createResponse(200)->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode($users)));
        return $response;
    }

    public static function getUser($userId, Response $response)
    {
        $responseFactory = new \Slim\Psr7\Factory\ResponseFactory();
        $streamFactory = new StreamFactory();

        $pdo = Database\Database::getInstance();

        // Check does user exist
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            $response = $responseFactory->createResponse(404)->withHeader('Content-Type', 'application/json')
                ->withBody($streamFactory->createStream(json_encode(['message' => 'User not found'])));
            return $response;
        }

        $response = $responseFactory->createResponse(200)->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode($user)));
        return $response;


    }

}
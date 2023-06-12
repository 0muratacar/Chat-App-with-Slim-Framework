<?php

namespace App\Models;

use App\Database;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\Psr17\ResponseFactory as ResponseFactory;
use Slim\Psr7\Stream;
use Slim\Psr7\Factory\StreamFactory;



class Message
{

    public static function send($groupId, $user, $message, Request $request, Response $response)
    {
        $responseFactory = new \Slim\Psr7\Factory\ResponseFactory();
        $streamFactory = new StreamFactory();

        $pdo = Database\Database::getInstance();

        // Check does group exist
        $stmt = $pdo->prepare('SELECT * FROM chat_groups WHERE id = ?');
        $stmt->execute([$groupId]);
        $group = $stmt->fetch();

        if (!$group) {
            $response = $responseFactory->createResponse(404)->withHeader('Content-Type', 'application/json')
                ->withBody($streamFactory->createStream(json_encode(['message' => 'Group not found'])));
            return $response;
        }

        // Check does user  exist in group
        $stmt = $pdo->prepare('SELECT * FROM user_groups WHERE user_id = ? AND group_id = ?');
        $stmt->execute([$user['id'], $groupId]);
        $userGroup = $stmt->fetch();

        if (!$userGroup) {
            $response = $responseFactory->createResponse(401)->withHeader('Content-Type', 'application/json')
                ->withBody($streamFactory->createStream(json_encode(['message' => 'You are not member of this group'])));
            return $response;

        }
        error_log("1");
        // Insert the message into the messages table
        $stmt = $pdo->prepare('INSERT INTO messages (user_id, sender, group_id, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([$user['id'], $user['username'], $groupId, $message]);

        error_log("2");

        $response = $responseFactory->createResponse(201)->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode(['message' => 'Message sent'])));
        return $response;
    }

    public static function getMessages($user, $groupId, Response $response)
    {
        $responseFactory = new \Slim\Psr7\Factory\ResponseFactory();
        $streamFactory = new StreamFactory();

        $pdo = Database\Database::getInstance();

        // Check does group exist
        $stmt = $pdo->prepare('SELECT * FROM chat_groups WHERE id = ?');
        $stmt->execute([$groupId]);
        $group = $stmt->fetch();

        if (!$group) {
            $response = $responseFactory->createResponse(404)->withHeader('Content-Type', 'application/json')
                ->withBody($streamFactory->createStream(json_encode(['message' => 'Group not found'])));
            return $response;
        }

        // Get messages from the group
        $stmt = $pdo->prepare('SELECT sender,message,created_at FROM messages WHERE group_id = ?');
        $stmt->execute([$groupId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = $responseFactory->createResponse(200)->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode($messages)));
        return $response;
    }



}
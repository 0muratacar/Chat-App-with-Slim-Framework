<?php

namespace App\Models;

use App\Database;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\Psr17\ResponseFactory as ResponseFactory;
use Slim\Psr7\Stream;
use Slim\Psr7\Factory\StreamFactory;
use OpenApi\Annotations as OA;


/**
 * @OA\Info(title="My First API", version="0.1")
 */
class Group
{

    /**
     * @OA\Post(
     *     path="/group",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public static function create($groupName, $user, Request $request, Response $response)
    {
        $responseFactory = new \Slim\Psr7\Factory\ResponseFactory();
        $streamFactory = new StreamFactory();

        $pdo = Database\Database::getInstance();

        // Check does group exist
        $stmt = $pdo->prepare('SELECT * FROM chat_groups WHERE name = ?');
        $stmt->execute([$groupName]);
        $group = $stmt->fetch();

        if ($group) {
            $response = $responseFactory->createResponse(400)->withHeader('Content-Type', 'application/json')
                ->withBody($streamFactory->createStream(json_encode(['message' => 'Group already exist'])));
            return $response;
        }

        // Insert the new group into the database
        $stmt = $pdo->prepare('INSERT INTO chat_groups ( name) VALUES ( ?)');
        $stmt->execute([$groupName]);

        $stmt = $pdo->prepare('SELECT id FROM chat_groups WHERE name = ?');
        $stmt->execute([$groupName]);
        $groupId = $stmt->fetchColumn();
        // $groupId = $pdo->lastInsertId();

        // Add the user to the group
        $stmt = $pdo->prepare('INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)');
        $stmt->execute([$user['id'], $groupId]);


        $response = $responseFactory->createResponse(201)->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode(['message' => 'Group created successfully'])));
        return $response;
    }

    public static function getAll(Response $response)
    {
        $responseFactory = new \Slim\Psr7\Factory\ResponseFactory();
        $streamFactory = new StreamFactory();

        $pdo = Database\Database::getInstance();

        $stmt = $pdo->query('SELECT * FROM chat_groups');
        $stmt->execute();
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $response = $responseFactory->createResponse(200)->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode($groups)));
        return $response;
    }

    public static function getGroupMembers($groupId, $user, Response $response)
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

        // Check does the user exist in group
        $stmt = $pdo->prepare('SELECT * FROM user_groups WHERE user_id = ? AND group_id = ?');
        $stmt->execute([$user['id'], $groupId]);
        $userGroup = $stmt->fetch();

        if (!$userGroup) {
            $response = $responseFactory->createResponse(401)->withHeader('Content-Type', 'application/json')
                ->withBody($streamFactory->createStream(json_encode(['message' => 'You are not authorized to view the users of this group.'])));
            return $response;
        }

        // Get user ids from group
        $stmt = $pdo->prepare('SELECT user_id FROM user_groups WHERE group_id = ?');
        $stmt->execute([$groupId]);
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Get user names from user ids.
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id IN ($placeholders)");
        $stmt->execute($userIds);
        $names = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $response = $responseFactory->createResponse(200)->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode($names)));
        return $response;


    }

    public static function join($groupId, $user, Response $response)
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

        // Check if the user already exist in group
        $stmt = $pdo->prepare('SELECT * FROM user_groups WHERE user_id = ? AND group_id = ?');
        $stmt->execute([$user['id'], $groupId]);
        $userGroup = $stmt->fetch();

        if ($userGroup) {
            $response = $responseFactory->createResponse(400)->withHeader('Content-Type', 'application/json')
                ->withBody($streamFactory->createStream(json_encode(['message' => 'You are already in this group'])));
            return $response;

        }

        // Add the user to the group
        $stmt = $pdo->prepare('INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)');
        $stmt->execute([$user['id'], $groupId]);


        $response = $responseFactory->createResponse(200)->withHeader('Content-Type', 'application/json')
            ->withBody($streamFactory->createStream(json_encode(['message' => 'You joined the group'])));
        return $response;

    }
}
<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="User APIs", version="0.1")
 */
class UserController
{

    /**
     * @OA\Post(
     *     path="/users/create",
     *     @OA\Response(response="201", description="User created.")
     * )
     */
    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $userName = $data['name'];

        $resp = User::create($userName, $request, $response);

        return $resp;
    }

    public function getAll(Request $request, Response $response, $args)
    {
        $users = User::getAll($response);

        return $users;
    }

    public function getUser(Request $request, Response $response, $args)
    {
        $userId = $args['id'];

        $groupUserMatch = User::getUser($userId, $response);

        return $groupUserMatch;
    }

}
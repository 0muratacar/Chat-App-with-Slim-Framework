<?php

namespace App\Controllers;

use App\Models\Message;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Message APIs", version="0.1")
 */
class MessageController
{

    /**
     * @OA\Post(
     *     path="/messages/send",
     *     @OA\Response(response="200", description="Message sent.")
     * )
     */
    public function send(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();
        $message = $data['message'];
        $groupId = $args['group_id'];
        $user = $request->getAttribute('user');


        $resp = Message::send($groupId, $user, $message, $request, $response);

        return $resp;
    }

    public function getMessages(Request $request, Response $response, $args)
    {
        $groupId = $args['group_id'];
        $user = $request->getAttribute('user');

        $messages = Message::getMessages($user, $groupId, $response);

        return $messages;
    }

    public function getSpesificUser(Request $request, Response $response, $args)
    {
        $userId = $args['id'];

        $groupUserMatch = User::getUser($userId, $response);

        return $groupUserMatch;
    }

}
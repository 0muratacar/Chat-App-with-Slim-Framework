<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Group;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Group APIs", version="0.1")
 */
class GroupController
{

    /**
     * @OA\Post(
     *     path="/groups/create",
     *     @OA\Response(response="201", description="Group created.")
     * )
     */
    public function create(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $data = $request->getParsedBody();
        $groupName = $data['name'];

        $group = Group::create($groupName, $user, $request, $response);

        return $group;
    }


    public function getAll(Request $request, Response $response, $args)
    {
        $groups = Group::getAll($response);

        return $groups;
    }

    public function getGroupMembers(Request $request, Response $response, $args)
    {
        // Get group ID from route parameters
        $groupId = $args['group_id'];
        $user = $request->getAttribute('user');

        $groupUserMatch = Group::getGroupMembers($groupId, $user, $response);

        return $groupUserMatch;
    }

    public function join(Request $request, Response $response, $args)
    {
        // Get group ID from route parameters
        $groupId = $args['group_id'];
        $user = $request->getAttribute('user');

        // Join the group
        $group = Group::join($groupId, $user, $response);

        return $group;
    }
}
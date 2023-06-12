<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Middleware\TokenMiddleware;
use App\Controllers\GroupController;
use App\Controllers\UserController;
use App\Controllers\MessageController;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {


    $tokenMiddleware = new TokenMiddleware();

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        return $response;
    });

    $app->group('/docs', function (RouteCollectorProxy $group) {

        $group->get('[/]', function (Request $request, Response $response) {
            $swaggerUiContent = file_get_contents(__DIR__ . '/swagger/dist/index.html');
            $response->getBody()->write($swaggerUiContent);
            return $response;
        });

        $group->get('/swagger-ui.css', function (Request $request, Response $response) {
            $content = file_get_contents(__DIR__ . '/swagger/dist/swagger-ui.css');
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'text/css');
        });

        $group->get('/index.css', function (Request $request, Response $response) {
            $content = file_get_contents(__DIR__ . '/swagger/dist/index.css');
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'text/css');
        });

        $group->get('/swagger-ui-bundle.js', function (Request $request, Response $response) {
            $content = file_get_contents(__DIR__ . '/swagger/dist/swagger-ui-bundle.js');
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'application/javascript');
        });

        $group->get('/swagger-ui-standalone-preset.js', function (Request $request, Response $response) {
            $content = file_get_contents(__DIR__ . '/swagger/dist/swagger-ui-standalone-preset.js');
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'application/javascript');
        });

        $group->get('/swagger-initializer.js', function (Request $request, Response $response) {
            $content = file_get_contents(__DIR__ . '/swagger/dist/swagger-initializer.js');
            $response->getBody()->write($content);
            return $response->withHeader('Content-Type', 'application/javascript');
        });
    });


    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    // Create a new user
    $app->post('/users', [UserController::class, 'create']);


    // Get all users
    $app->get('/users', [UserController::class, 'getAll']);

    // Get a specific user by ID
    /**
     * Get user by ID.
     *
     * @Route("/users/{id}", methods={"GET"})
     *
     * @param int $id User's unique ID.
     */
    $app->get('/users/{id}', [UserController::class, 'getUser']);



    // Create a new group and include the user in the group
    $app->post('/groups', [GroupController::class, 'create'])->add($tokenMiddleware);

    // Get all groups
    $app->get('/groups', [GroupController::class, 'getAll']);

    // Get users of a group
    // You should member of the group to see group members
    $app->get('/groups/{group_id}/users', [GroupController::class, 'getGroupMembers'])->add($tokenMiddleware);

    // Join the group
    $app->post('/groups/{group_id}/join', GroupController::class . ':join')->add($tokenMiddleware);


    // send message to the group
    $app->post('/messages/{group_id}', MessageController::class . ':send')->add($tokenMiddleware);

    // get messages from the group
    $app->get('/messages/{group_id}', MessageController::class . ':getMessages')->add($tokenMiddleware);

};
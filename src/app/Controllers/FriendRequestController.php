<?php

namespace App\Controllers;

use App\Services\FriendshipService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \App\Helpers\Session;

use Slim\Views\Twig;
use \App\Models\Post;
use \App\Models\User;
use \App\Models\FriendRequest;
use \App\Models\Friendship;

class FriendRequestController extends BaseController
{
    public function create(Request $request, Response $response, array $args)
    {
        // friendship_same_user, friendship_exists, friend_request_exists, friendship_created
        $friendRequestFrom = Session::currentUser();
        $friendRequestTo = (int) $args['user_id'];

        $result = (new FriendshipService)->processRequest($friendRequestFrom, $friendRequestTo);

        switch ($result)
        {
            case 'FRIEND_SAME_USER':
                $response->withStatus(400);
                break;
            case 'FRIEND_REQUEST_EXISTS':
                $response->withStatus(200);
                break;
            case 'FRIENDSHIP_EXISTS':
                $response->withStatus(200);
                break;
            case 'FRIENDSHIP_CREATED':
                $response->withStatus(200);
                break;
            case 'FRIEND_REQUEST_CREATED':
                $response->getBody()->write('Success!');
                break;
        }


        return $response;
    }

    public function destroy(Request $request, Response $response, array $args)
    {
        $friendRequestFrom = Session::currentUser();
        $friendRequestTo = (int) $args['user_id'];

        if ($friendRequestFrom === $friendRequestTo) {
            return $response->withStatus(404);
        }

        $friendRequest = FriendRequest::find($friendRequestFrom, $friendRequestTo);

        if (!$friendRequest) {
            return $response->withStatus(404);
        }

        FriendRequest::destroy($friendRequest);

        return $response->withStatus(200);
    }
}
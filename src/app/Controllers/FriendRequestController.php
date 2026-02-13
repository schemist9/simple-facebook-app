<?php

namespace App\Controllers;

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
        $friendRequestFrom = Session::currentUser();
        $friendRequestTo = (int) $args['user_id'];

        if ($friendRequestFrom === $friendRequestTo) {
            $message = "I knew you'd try to do it, my friend. I am one step ahead of you. Or am I?";
            return $response->withStatus(400);
        }

        $friendRequestExists = FriendRequest::findByFromAndToId($friendRequestFrom, $friendRequestTo);
        if ($friendRequestExists) {
            return $response->withStatus(200);
        }

        $friendshipExists = Friendship::findByFriends($friendRequestFrom, $friendRequestTo);
        if ($friendshipExists) {
            return $response->withStatus(200);
        }

        $incomingFriendRequestExists = FriendRequest::findByFromAndToId($friendRequestTo, $friendRequestFrom);
        if ($incomingFriendRequestExists) {
            $friendship = new Friendship(['user_1' => $friendRequestFrom, 'user_2' => $friendRequestTo]);
            $friendship->create();
            FriendRequest::destroy($incomingFriendRequestExists);

            return $response;
        }

        $friendRequest = new FriendRequest([ 'from_id' => $friendRequestFrom, 'to_id' => $friendRequestTo ]);
        $friendRequest->create();

        $response->getBody()->write('Success!');
        return $response;
    }

    public function destroy(Request $request, Response $response, array $args)
    {
        $friendRequestFrom = Session::currentUser();
        $friendRequestTo = (int) $args['user_id'];

        if ($friendRequestFrom === $friendRequestTo) {
            return $response->withStatus(404);
        }

        $friendRequest = FriendRequest::findByFromAndToId($friendRequestFrom, $friendRequestTo);

        if (!$friendRequest) {
            return $response->withStatus(404);
        }

        FriendRequest::destroy($friendRequest);

        return $response->withStatus(200);
    }
}
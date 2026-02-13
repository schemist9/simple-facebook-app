<?php

namespace App\Controllers;

use App\Helpers\Session;
use App\Models\Friendship;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class FriendshipController
{
    public function destroy(Request $request, Response $response, array $args)
    {
        $loggedUserId = Session::currentUser();
        $friendId = (int) $args['user_id'];

        $friendship = Friendship::findByFriends($loggedUserId, $friendId);

        if (!$friendship) {
            return $response->withStatus(404);
        }

        Friendship::destroy($friendship);

        return $response->withStatus(200);
    }
}
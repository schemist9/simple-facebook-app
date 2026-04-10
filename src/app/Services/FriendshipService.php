<?php

namespace App\Services;

use App\Helpers\Session;
use App\Models\FriendRequest;
use App\Models\Friendship;
use App\Models\Post;

class FriendshipService
{
    public function __construct()
    {
    }

    public function processRequest(int $user_1, int $user_2): string
    {
        $friendRequestFrom = $user_1;
        $friendRequestTo = $user_2;

        if ($friendRequestFrom === $friendRequestTo) {
            return 'FRIEND_SAME_USER';
        }

        $friendRequestExists = FriendRequest::find($friendRequestFrom, $friendRequestTo);
        if ($friendRequestExists) {
            return 'FRIEND_REQUEST_EXISTS';
        }

        $friendshipExists = Friendship::find($friendRequestFrom, $friendRequestTo);
        if ($friendshipExists) {
            return 'FRIENDSHIP_EXISTS';
        }

        $incomingFriendRequestExists = FriendRequest::find($friendRequestTo, $friendRequestFrom);
        if ($incomingFriendRequestExists) {
            $friendship = new Friendship(['user_1' => $friendRequestFrom, 'user_2' => $friendRequestTo]);
            $friendship->create();
            FriendRequest::destroy($incomingFriendRequestExists);

            return 'FRIENDSHIP_CREATED';
        }

        $friendRequest = new FriendRequest([ 'from_id' => $friendRequestFrom, 'to_id' => $friendRequestTo ]);
        $friendRequest->create();

        return 'FRIEND_REQUEST_CREATED';
    }
}
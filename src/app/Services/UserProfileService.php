<?php

namespace App\Services;

use App\Helpers\Session;
use App\Models\FriendRequest;
use App\Models\Friendship;
use App\Models\Post;

class UserProfileService
{
    public function __construct()
    {
    }

    public function showUserProfile(int $wallId)
    {
        $friendRequests = FriendRequest::findTo($wallId);
        $friends = Friendship::findByUser($wallId);

        $loggedUser = Session::currentUser();

        if ($loggedUser) {
            $loggedUserFriends = Friendship::findByUser($loggedUser);

            $friendshipExists = Friendship::findByFriends($loggedUser, $wallId);
            $incomingFriendRequestExists = FriendRequest::findByFromAndToId($wallId, $loggedUser);
            $outgoingFriendRequestExists = FriendRequest::findByFromAndToId($loggedUser, $wallId);
        }

        $posts = Post::getByWallId($wallId);
    
        return [
            'wall_user_posts' => $posts,
            'wall_user_friends' => $friends,
            'wall_user_friend_requests' => $friendRequests,
            'wall_user_logged_user_friends' => $loggedUserFriends ?? [],
            'friendship_exists' => $friendshipExists ?? false,
            'incoming_friend_request_exists' => $incomingFriendRequestExists ?? [],
            'outgoing_friend_request_exists' => $outgoingFriendRequestExists ?? []
        ];

    }
}
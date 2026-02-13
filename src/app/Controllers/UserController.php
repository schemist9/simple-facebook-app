<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \App\Helpers\Session;

use Slim\Views\Twig;
use \App\Models\User;
use \App\Models\Post;
use \App\Models\PostLike;
use \App\Models\UserValidator;
use \App\Models\FriendRequest;
use \App\Models\Friendship;

class UserController
{
    public function __construct(private Twig $twig)
    {

    }

    public function show(Request $request, Response $response, array $args) {
        $id = (int) $args['id'];
        $user = User::find($id);
        $friendRequests = FriendRequest::findTo($id);
        $friends = Friendship::findByUser($id);

        $currentUser = Session::currentUser();

        if ($currentUser) {
            $currentUserFriends = Friendship::findByUser($currentUser);

            $friendshipExists = Friendship::exists($currentUser, $id);
            $incomingFriendRequestExists = FriendRequest::findByFromAndToId($id, $currentUser);
            $outgoingFriendRequestExists = FriendRequest::findByFromAndToId($currentUser, $id);
        }
        if (!$user) {
            return $response->withStatus(404);
        }

        $posts = Post::getByWallId($id);
        
        return $this->twig->render($response, 'users/show.html', [
            'firstname' => $user['firstname'],
            'surname' => $user['surname'],
            'email' => $user['email'],
            'id' => $user['id'],
            'avatar' => $user['avatar'],
            'loggedIn' => Session::loggedIn(),
            'wallUserId' => $id,
            'friend_requests' => $friendRequests,
            'posts' => $posts,
            'friends' => $friends,
            'friendship_exists' => $friendshipExists ?? false,
            'incoming_friend_request_exists' => $incomingFriendRequestExists ?? [],
            'outgoing_friend_request_exists' => $outgoingFriendRequestExists ?? []
        ]);
    }

    public function new(Request $request, Response $response) {
        if (Session::loggedIn()) {
            header('Location: /');
            exit;
        }

        return $this->twig->render($response, 'users/new.html');
    }

    public function create(Request $request, Response $response) {  
        $requestData = $request->getParsedBody();

        if (!is_array($requestData)) {
            $response->getBody()->write('Invalid form data');
            return $response->withStatus(400);
        }

        $allowedInput = ['firstname', 'surname', 'email', 'password'];

        foreach ($requestData as $key => $value) {
            if (!in_array($key, $allowedInput, true)) {
                $response->getBody()->write('Error');
                return $response;
            }
        }

        $errors = (new UserValidator())->validate($requestData);

        if (!empty($errors)) {
            return $this->twig->render($response, 'users/new.html', $errors);
        }

        $user = (new User($requestData));
        
        $userId = $user->id;

        if (!empty($user->errors())) {
            return $this->twig->render($response, 'users/new.html', $user->errors());
        }

        $_SESSION['user_id'] = $userId;
        
        return $response
            ->withHeader('Location', '/')
            ->withStatus(303);
    }
}
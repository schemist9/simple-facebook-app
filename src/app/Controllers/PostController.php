<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \App\Helpers\Session;

use Slim\Views\Twig;
use \App\Models\Post;
use \App\Models\User;
use \App\Models\PostValidator;

class PostController extends BaseController
{
    public function __construct(private Twig $twig)
    {

    }

    public function create(Request $request, Response $response, array $args) {
        $requestData = $request->getParsedBody();
        $errorReponse = $this->filterParams($request, $response, $requestData, ['text']);

        if ($errorReponse) {
            return $errorResponse;
        }

        $wallUserId = $args['user_id'];
        $wallUser = User::find($wallUserId);
        if (!$wallUser) {
            return $response
                ->withHeader('Location', "/users/$wallUserId");
        }
        $userId = Session::currentUser();
        $requestData['user_id'] = $userId;
        $requestData['user_wall_id'] = $wallUserId;
        $post = (new Post($requestData));

        if (!empty($post->errors())) {
            return $this-twig->render($response, 'posts/new.html', $post->errors());
        }
        
        return $response
            ->withHeader('Location', "/users/$wallUserId")
            ->withStatus(303);
    }

}



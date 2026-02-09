<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \App\Helpers\Session;

use Slim\Views\Twig;
use \App\Models\Post;
use \App\Models\PostLike;
use \App\Models\PostValidator;

class PostLikeController extends BaseController
{
    public function create(Request $request, Response $response, array $args)
    {
        if (!Session::loggedIn()) {
            return $response
                ->withStatus(401);
        }

        $postId = (int) $args['id'];
        $post = Post::find($postId);

        if (empty($post)) {
            return $response
                ->withStatus(404);
        }

        $postOwnerId = $post['user_id'];

        $requestData = $request->getParsedBody();

        // $errorResponse = $this->filterParams($request, $response, $requestData, ['post_id']);

        // if ($errorResponse) {
        //     return $errorResponse;
        // }

        // $postId = $requestData['post_id'];

        $currentUserId = Session::currentUser();

        $likeExists = PostLike::find($postId, $currentUserId);

        if (!$likeExists) {
            new PostLike($postId, $currentUserId);
        }

        return $response
            ->withStatus(200);
    }

    public function destroy(Request $request, Response $response)
    {

    }
}
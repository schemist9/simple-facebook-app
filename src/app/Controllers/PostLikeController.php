<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \App\Helpers\Session;

use Slim\Views\Twig;
use \App\Models\Post;
use \App\Models\Like;
use \App\Models\PostValidator;

class PostLikeController extends BaseController
{
    public function create(Request $request, Response $response, array $args)
    {
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

        $likeExists = Like::find($currentUserId, $postId, 'post');

        if (!$likeExists) {
            new Like($currentUserId, $postId, 'post');
        }

        return $response
            ->withStatus(200);
    }

    public function destroy(Request $request, Response $response)
    {

    }
}
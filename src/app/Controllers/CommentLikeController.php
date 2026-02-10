<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \App\Helpers\Session;

use Slim\Views\Twig;
use \App\Models\Comment;
use \App\Models\Like;
use \App\Models\PostValidator;

class CommentLikeController extends BaseController
{
    public function create(Request $request, Response $response, array $args)
    {
        if (!Session::loggedIn()) {
            return $response
                ->withStatus(401);
        }

        $commentId = (int) $args['comment_id'];
        $comment = Comment::find($commentId);

        if (empty($commentId)) {
            return $response
                ->withStatus(404);
        }

        $commentOwnerId = $comment['user_id'];

        $requestData = $request->getParsedBody();

        // $errorResponse = $this->filterParams($request, $response, $requestData, ['post_id']);

        // if ($errorResponse) {
        //     return $errorResponse;
        // }

        // $postId = $requestData['post_id'];

        $currentUserId = Session::currentUser();

        $likeExists = Like::find($currentUserId, $commentId, 'comment');

        if (!$likeExists) {
            new Like($currentUserId, $commentId, 'comment');
        }

        return $response
            ->withStatus(200);
    }

    public function destroy(Request $request, Response $response)
    {

    }
}
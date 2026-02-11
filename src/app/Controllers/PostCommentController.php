<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \App\Helpers\Session;

use Slim\Views\Twig;
use \App\Models\Post;
use \App\Models\User;
use \App\Models\Comment;

class PostCommentController extends BaseController
{
    public function __construct(private Twig $twig)
    {

    }

    public function create(Request $request, Response $response, array $args) 
    {
        if (!Session::loggedIn()) {
            return $response
                ->withHeader('Location', '/')
                ->withStatus(303);
        }
        $requestData = $request->getParsedBody();
        $postId = $args['post_id'];

        $post = Post::find($postId);

        if (!$post) {
            return $response
                ->withHeader('Location', '/')
                ->withStatus(303);
        }

        $userId = Session::currentUser();
        $postAuthorId = $post['user_id'];

        $comment = new Comment([
            'text' => $requestData['text'],
            'commentable_id' => $postId,
            'commentable_type' => 'post',
            'user_id' => $userId
        ]);
        $comment->create();

        // if ($comment->errors()) {
        //     return $response
        //         ->withHeader('Location', '/')
        //         ->withStatus(303);
        // }

        return $response
            ->withHeader('Location', '/users/' . $postAuthorId)
            ->withStatus(303);
    }
}

<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \App\Helpers\Session;

use Slim\Views\Twig;
use \App\Models\Post;
use \App\Models\PostValidator;

class PostController
{
    public function __construct(private Twig $twig)
    {

    }

    public function new(Request $request, Response $response) {
        if (!Session::loggedIn()) {
            header('Location: /');
            exit;
        }

        return $this->twig->render($response, 'posts/new.html');
    }

    public function create(Request $request, Response $response) {  
        if (!Session::loggedIn()) {
            header('Location: /');
            exit;
        }

        $requestData = $request->getParsedBody();

        if (!is_array($requestData)) {
            $response->getBody()->write('Invalid form data');
            return $response->withStatus(400);
        }

        $allowedInput = ['text'];

        foreach ($requestData as $key => $value) {
            if (!in_array($key, $allowedInput, true)) {
                $response->getBody()->write('Error');
                return $response;
            }
        }

        $requestData['user_id'] = Session::currentUser()['id'];
        $post = (new Post($requestData));

        if (!empty($post->errors())) {
            return $this-twig->render($response, 'posts/new.html', $post->errors());
        }
        
        return $response
            ->withHeader('Location', '/')
            ->withStatus(303);
    }

}



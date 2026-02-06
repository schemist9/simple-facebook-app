<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \App\Helpers\Session;

use Slim\Views\Twig;
use \App\Models\User;
use \App\Models\UserValidator;

class IndexController
{
    public function __construct(private Twig $twig) 
    {

    }

    public function index(Request $request, Response $response) {
        if (isset($_SESSION['user_id'])) {
            return $this->twig->render($response, 'index.html', [
                'loggedIn' => Session::loggedIn()
            ]);
        } 

        return $this->twig->render($response, 'users/new.html');
    }
}
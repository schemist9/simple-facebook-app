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
        $users = User::all();
        return $this->twig->render($response, 'index.html', [
            'loggedIn' => Session::loggedIn()
        ] + [ 'users' => $users ]);
    }
}
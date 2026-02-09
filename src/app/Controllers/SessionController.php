<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \App\Helpers\Session;

use Slim\Views\Twig;
use \App\Models\User;

class SessionController
{
    public function __construct(private Twig $twig)
    {
        
    }

    public function new(Request $request, Response $response) 
    {
        if (Session::loggedIn()) {
            return $response
                ->withHeader('Location', '/')
                ->withStatus(303);
        }

        return $this->twig->render($response, 'sessions/new.html');
    }

    public function create(Request $request, Response $response) 
    {
        $requestData = $request->getParsedBody();

        if (!is_array($requestData)) {
            $response->getBody()->write('Invalid form data');
            return $response->withStatus(400);
        }

        $allowedInput = ['email', 'password'];

        foreach ($requestData as $key => $value) {
            if (!in_array($key, $allowedInput, true)) {
                $response->getBody()->write('Error');
                return $response;
            }
        }

        $user = User::findByEmail($requestData['email']);
        $view = $this->twig::fromRequest($request);

        if (!$user) {
            return $view->render($response, 'sessions/new.html', [
                'error' => 'Invalid credentials'
            ]);
        }

        if (password_verify($requestData['password'], $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            return $response
                ->withHeader('Location', '/')
                ->withStatus(303);
        } else {
            return $view->render($response, 'sessions/new.html', [
                'error' => 'Invalid credentials'
            ]);
        }
    }

    public function destroy(Request $request, Response $response)
    {
        if (!Session::loggedIn()) {
            header('Location: /');
            exit;
        }

        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 1,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
        session_destroy();
        
        return $response
            ->withHeader('Location', '/')
            ->withStatus(303);
    }
}
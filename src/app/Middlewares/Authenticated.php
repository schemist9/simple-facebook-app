<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use \App\Helpers\Session;

class Authenticated
{
    public function __invoke(Request $request, Handler $handler)
    {
        if (Session::loggedIn()) {
            return $handler->handle($request);
        }

        $response = new \Slim\Psr7\Response();
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }
}

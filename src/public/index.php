<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
require '../vendor/autoload.php';

$app = AppFactory::create();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

App\App::run();
$pdo = App\DB::get();

$app->get('/', function (Request $request, Response $response) {
    $html = file_get_contents('../views/users/new.html');
    $response->getBody()->write($html);
    return $response;
});

$app->post('/users', function (Request $request, Response $response) {  
    
});

// Run app
$app->run();

if ($_SERVER['REQUEST_URI'] === '/users') {
    // validate each field
    // if OK, save to DB and create a new session
    // if failed, send validation errors back to the client
}

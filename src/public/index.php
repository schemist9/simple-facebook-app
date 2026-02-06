<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use DI\Container;

use App\Models\UserValidator;
use App\Models\User;

use App\Controllers\SessionController;
use App\Controllers\UserController;

require '../vendor/autoload.php';

session_start();

$container = new Container();

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addRoutingMiddleware();
$twig = Twig::create(__DIR__ . '/../views', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$container->set(Twig::class, function() {
    return Twig::create(__DIR__ . '/../views', ['cache' => false]);
});

App\App::run();
$pdo = App\DB::get();

$app->get('/', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);

    if (isset($_SESSION['user_id'])) {
        return $view->render($response, 'index.html', [
            'loggedIn' => App\Helpers\Session::loggedIn()
        ]);
    } 

    return $view->render($response, 'users/new.html');
});

$app->get('/register', [UserController::class, 'new']);

$app->get('/login', [SessionController::class, 'new']);
$app->post('/login', [SessionController::class, 'create']);
$app->get('/logout', [SessionController::class, 'destroy']);


$app->post('/register', [UserController::class, 'create']);


$app->get('/users/{id}', [UserController::class, 'show']);

$app->patch('/users/:id', function (Request $request, Response $response, int $id) {
    
});

// Run app
$app->run();


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
use App\Controllers\IndexController;

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

$app->get('/', [IndexController::class, 'index']);


$app->get('/login', [SessionController::class, 'new']);
$app->post('/login', [SessionController::class, 'create']);
$app->get('/logout', [SessionController::class, 'destroy']);

$app->get('/register', [UserController::class, 'new']);
$app->post('/register', [UserController::class, 'create']);
$app->get('/users/{id}', [UserController::class, 'show']);


// Run app
$app->run();


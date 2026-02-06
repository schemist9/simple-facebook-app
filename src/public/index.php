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

$app->get('/register', function (Request $request, Response $response) {
    if (App\Helpers\Session::loggedIn()) {
        header('Location: /');
        exit;
    }

    $view = Twig::fromRequest($request);
    return $view->render($response, 'users/new.html');
});

$app->get('/login', [SessionController::class, 'new']);
$app->post('/login', [SessionController::class, 'create']);
$app->get('/logout', [SessionController::class, 'destroy']);


$app->post('/register', function (Request $request, Response $response) {  
    $requestData = $request->getParsedBody();

    if (!is_array($requestData)) {
        $response->getBody()->write('Invalid form data');
        return $response->withStatus(400);
    }

    $allowedInput = ['firstname', 'surname', 'email', 'password'];

    foreach ($requestData as $key => $value) {
        if (!in_array($key, $allowedInput, true)) {
            $response->getBody()->write('Error');
            return $response;
        }
    }

    $errors = (new UserValidator())->validate($requestData);
    $view = Twig::fromRequest($request);

    if (!empty($errors)) {
        return $view->render($response, 'users/new.html', $errors);
    }

    $user = (new User());
    $userId = $user->create($requestData);

    if (!empty($user->errors())) {
        return $view->render($response, 'users/new.html', $user->errors());
    }

    $_SESSION['user_id'] = $userId;
    
    return $response
        ->withHeader('Location', '/')
        ->withStatus(303);
});


$app->get('/users/:id', function (Request $request, Response $response, int $id) {
    $user = User::find($id);
    if (!$user) {
        return $response->withStatus(404);
    }

    $view = Twig::fromRequest($request);

    return $view->render($response, 'users/show.html', [
        'firstname' => $user['firstname'],
        'surname' => $user['surname'],
        'email' => $user['email']
    ]);
});

$app->patch('/users/:id', function (Request $request, Response $response, int $id) {
    
});

// Run app
$app->run();


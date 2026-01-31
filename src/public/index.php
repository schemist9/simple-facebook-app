<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use App\Models\UserValidator;
use App\Models\User;

require '../vendor/autoload.php';

session_start();

$app = AppFactory::create();
$app->addRoutingMiddleware();
$twig = Twig::create(__DIR__ . '/../views', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

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

$app->get('/logout', function (Request $request, Response $response) {
    if (!App\Helpers\Session::loggedIn()) {
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
});

$app->get('/register', function (Request $request, Response $response) {
    if (App\Helpers\Session::loggedIn()) {
        header('Location: /');
        exit;
    }

    $view = Twig::fromRequest($request);
    return $view->render($response, 'users/new.html');
});

$app->get('/login', function (Request $request, Response $response) {
    if (App\Helpers\Session::loggedIn()) {
        return $response
            ->withHeader('Location', '/')
            ->withStatus(303);
    }

    $view = Twig::fromRequest($request);
    return $view->render($response, 'sessions/new.html');
});

$app->post('/login', function (Request $request, Response $response) {
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
    $view = Twig::fromRequest($request);

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
});

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

// Run app
$app->run();


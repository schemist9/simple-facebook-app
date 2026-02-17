<?php

use App\Controllers\FriendshipController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\TwigFunction;

use DI\Container;

use App\Models\UserValidator;
use App\Models\User;

use App\Controllers\SessionController;
use App\Controllers\UserController;
use App\Controllers\IndexController;
use App\Controllers\PostController;
use App\Controllers\PostLikeController;
use App\Controllers\PostCommentController;
use App\Controllers\CommentLikeController;
use App\Controllers\FriendRequestController;
use App\Middlewares\Authenticated;
use App\Helpers\Views\LikeHelper;

require '../vendor/autoload.php';

session_start();

$container = new Container();

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addRoutingMiddleware();

$twig = Twig::create(__DIR__ . '/../views', ['cache' => false]);
$likeHelper = new LikeHelper();

$app->add(TwigMiddleware::create($app, $twig));

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$container->set(Twig::class, function() use ($likeHelper) {
    $twg = Twig::create(__DIR__ . '/../views', ['cache' => false]);
    $twg->getEnvironment()->addFunction(
        new TwigFunction('user_liked_post', [$likeHelper, 'userLikedPost'])
    );
    $twg->getEnvironment()->addFunction(
        new TwigFunction('user_liked_comment', [$likeHelper, 'userLikedComment'])
    );
    return $twg;
});

$container->set(User::class, function() {
    return User::class;
});

// $app->add(TwigMiddleware::createFromContainer($app, Twig::class));

App\App::run();
$pdo = App\DB::get();

$app->get('/', [IndexController::class, 'index']);

$app->get('/login', [SessionController::class, 'new']);
$app->post('/login', [SessionController::class, 'create']);
$app->get('/logout', [SessionController::class, 'destroy']);

$app->get('/register', [UserController::class, 'new']);
$app->post('/register', [UserController::class, 'create']);
$app->get('/users/{id}', [UserController::class, 'show']);

$app->post('/users/{user_id}/posts', [PostController::class, 'create'])->add(new Authenticated());

$app->post('/posts/{id}/likes', [PostLikeController::class, 'create'])->add(new Authenticated());
$app->delete('/posts/{id}/likes', [PostLikeController::class, 'destroy'])->add(new Authenticated());

$app->post('/comments/{comment_id}/likes', [CommentLikeController::class, 'create'])->add(new Authenticated());
$app->delete('/comments/{comment_id}/likes', [CommentLikeController::class, 'destroy'])->add(new Authenticated());


$app->post('/posts/{post_id}/comments', [PostCommentController::class, 'create'])->add(new Authenticated());

$app->post('/users/{user_id}/friend_requests', [FriendRequestController::class, 'create'])->add(new Authenticated());
$app->delete('/users/{user_id}/friend_requests', [FriendRequestController::class, 'destroy'])->add(new Authenticated());
$app->delete('/users/{user_id}/friends', [FriendshipController::class, 'destroy'])->add(new Authenticated());

// Run app
$app->run();


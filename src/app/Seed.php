<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\App;

require __DIR__ . "/../vendor/autoload.php";

App::run();

new User([ 'firstname' => 'John', 'surname' => 'Doe', 'email'=>'hello@hello.hello', 'password'=>'hello']);
new User([ 'firstname' => 'Jane', 'surname' => 'Doe', 'email'=>'why@hell.not', 'password'=>'hello']);

new Post(['user_id' => 1, 'user_wall_id' => 1, 'text'=>"This is John Doe's first post!"]);
new Post(['user_id' => 1, 'user_wall_id' => 1, 'text'=>"Sometimes I think to myself: what am I really doing here?"]);
new Post(['user_id' => 2, 'user_wall_id' => 2, 'text' => "This is Jane Doe's first post!"]);

$comment = new Comment(['user_id' => 1, 'text' => 'Hello, world!', 'commentable_id' => 1, 'commentable_type' => 'post']);
$comment->create();

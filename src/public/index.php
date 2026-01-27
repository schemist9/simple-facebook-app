<?php

require '../vendor/autoload.php';

App\App::run();
$pdo = App\DB::get();

$query = 'SELECT * FROM users';
$stmt = $pdo->prepare($query);
$stmt->execute();
$res = $stmt->fetchAll();

if ($_SERVER['REQUEST_URI'] === '/users') {
    $requiredFields = ['firstname', 'surname', 'email', 'password'];
    if (array_any($requiredFields, fn($field) => !array_key_exists($field, $_POST))) {
        echo 'Are you trying to hack me?';
        return null;
    }

    // validate each field
    // if OK, save to DB and create a new session
    // if failed, send validation errors back to the client
}

require '../views/users/new.html';
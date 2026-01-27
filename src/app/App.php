<?php

namespace App;

class App
{
    private static function initDB()
    {
        $host = 'database';
        $port = 5432;
        $db = 'db';
        $user = 'postgres';
        $password = 'password';

        \App\DB::init($host, $port, $db, $user, $password);
    }

    public static function run()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');

        self::initDB();
    }
}
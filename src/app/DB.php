<?php

namespace App;

class DB
{
    private static \PDO $pdo;

    public static function init(
        string $host,
        int $port,
        string $db,
        string $user,
        string $password
    )
    {
        $dsn = "pgsql:host=$host;port=$port;dbname=$db";

        try {
            self::$pdo = new \PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function get(): \PDO
    {
        return self::$pdo;
    }
}
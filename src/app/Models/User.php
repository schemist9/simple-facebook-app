<?php

namespace App\Models;

class User
{
    private array $errors = [];

    public function errors()
    {
        return $this->errors;
    }

    public function create(array $user): ?string
    {
        if (self::emailExists($user['email'])) {
            $this->errors['email'] = 'Email already exists';
            return null;
        }
        $pdo = \App\DB::get();
            $query = 'INSERT INTO users (firstname, surname, email, password_hash)
                    VALUES (:firstname, :surname, :email, :password)';
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':firstname' => $user['firstname'],
            ':surname' => $user['surname'],
            ':email' => $user['email'],
            ':password' => password_hash($user['password'], PASSWORD_DEFAULT)
        ]);

        return $pdo->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        $pdo = \App\DB::get();
        $query = 'SELECT * FROM users WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':id' => $id ]);
        $res = $stmt->fetch();
        if (!$res) return null;
        return $res;
    }

    public static function findByEmail(string $email): ?array
    {
        $pdo = \App\DB::get();
        $query = 'SELECT * FROM users WHERE email = :email';
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':email' => $email ]);
        $res = $stmt->fetch();
        if (!$res) return null;
        return $res;
    }

    private static function emailExists(string $email) 
    {
        return self::findByEmail($email);
    }
}
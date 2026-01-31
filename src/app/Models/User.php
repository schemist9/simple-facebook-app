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
        if ($this->emailExists($user['email'])) {
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
        return $res;
    }

    private function emailExists(string $email) 
    {
        return false;
    }
}
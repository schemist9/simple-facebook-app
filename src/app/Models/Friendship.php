<?php

namespace App\Models;
use App\Models\CommentValidator;

class Friendship
{
    private array $errors = [];
    public ?int $user_1 = null;
    public ?int $user_2 = null;

    public function __construct(array $data)
    {
        $this->user_1 = $data['user_1'];
        $this->user_2 = $data['user_2'];
    }


    public function create()
    {
        $pdo = \App\DB::get();
        $query = "INSERT INTO users_friendships (user_1, user_2) VALUES (:user_1, :user_2)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':user_1' => $this->user_1,
            ':user_2' => $this->user_2
        ]);
        return $stmt;
    }

    public static function exists(int $user_1, int $user_2)
    {
        $pdo = \App\DB::get();
        $query = "SELECT * FROM users_friendships WHERE 
            (user_1 = :user_1 AND user_2 = :user_2) 
                OR
            (user_2 = :user_1 AND user_1 = :user_2)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':user_1' => $user_1, ':user_2' => $user_2 ]);
        return !empty($stmt->fetch());
    }

    public static function findByUser(int $userId)
    {
        $pdo = \App\DB::get();
        $query = "SELECT u.firstname as friend_firstname, u.surname as friend_surname, u.avatar as friend_avatar, u.id as friend_id FROM users_friendships f
            INNER JOIN users u
                ON (u.id = f.user_1 OR u.id = f.user_2)
            WHERE 
            (user_1 = :user OR user_2 = :user)
            AND u.id != :user";
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':user' => $userId ]);
        return $stmt->fetchAll();
    }
}
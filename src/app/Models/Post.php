<?php

namespace App\Models;

class Post
{
    private array $errors = [];

    public function __construct(array $data)
    {
        $errors = PostValidator::validate($data);
        if (!$errors) {
            $this->create($data);
        }
    }

    private function create(array $data)
    {
        $pdo = \App\DB::get();
        $query = "INSERT INTO posts (text, user_id) VALUES (:text, :user_id)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':text' => $data['text'],
            ':user_id' => $data['user_id']
        ]);
    }

    public static function getByUserId(int $userId)
    {
        $pdo = \App\DB::get();
        $query = "
            SELECT posts.text, posts.created_at, users.firstname AS author_name, users.surname AS author_surname FROM posts
                INNER JOIN users 
                    ON posts.user_id = users.id
                WHERE posts.user_id = :user_id
            ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':user_id' => $userId
        ]);
        $posts = $stmt->fetchAll();
        
        return $posts;
    }
    
    public function errors(): array
    {
        return $this->errors;
    }
}
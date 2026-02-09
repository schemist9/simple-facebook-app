<?php

namespace App\Models;

class Post
{
    private array $errors = [];
    public ?int $userId = null;

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
        $query = "INSERT INTO posts (text, user_id, user_wall_id) VALUES (:text, :user_id, :wall_user_id)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':text' => $data['text'],
            ':user_id' => $data['user_id'],
            ':wall_user_id' => $data['user_wall_id']
        ]);
        $this->userId = $data['user_id'];
    }

    public static function find(int $id)
    {
        $pdo = \App\DB::get();
        $query = "SELECT * FROM posts WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':id' => $id ]);
        return $stmt->fetch();
    }

    public static function getByWallId(int $wallId)
    {
        $pdo = \App\DB::get();
        $query = "
            SELECT COUNT(posts_likes.id) AS post_likes, posts.id, posts.text, posts.created_at, users.firstname AS author_name, users.surname AS author_surname FROM posts
                INNER JOIN users 
                    ON posts.user_id = users.id
                LEFT JOIN posts_likes
                    ON posts.id = posts_likes.post_id
                WHERE posts.user_wall_id = :user_wall_id
                GROUP BY (posts.id, users.id)
            ";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':user_wall_id' => $wallId
        ]);
        $posts = $stmt->fetchAll();


        $commentsQuery = "
            SELECT comments.text, comments.user_id, users.firstname, users.surname FROM comments
                INNER JOIN users
                    ON users.id = comments.user_id
                WHERE posts.id = ANY(:post_ids)
        ";

        $stmt = $pdo->prepare($commentsQuery);
        $stmt->execute();
        $comments = $stmt->fetchAll();
        
        return $posts;
    }
    
    public function errors(): array
    {
        return $this->errors;
    }
}
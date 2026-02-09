<?php

namespace App\Models;

class PostLike
{
    public function __construct(int $postId, int $userId)
    {
        $pdo = \App\DB::get();
        $query = "INSERT INTO posts_likes (post_id, user_id) VALUES (:post_id, :user_id)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':post_id' => $postId,
            ':user_id' => $userId
        ]);
    }

    public static function find(int $id, int $userId)
    {
        $pdo = \App\DB::get();
        $query = "SELECT * FROM posts_likes WHERE post_id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':id' => $id, ':user_id' => $userId ]);
        return $stmt->fetch();
    }

    public static function getByPostId(int $postId)
    {
        $pdo = \App\DB::get();
        $query = "SELECT COUNT(*) FROM posts_likes WHERE post_id = :post_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':post_id' => $postId ]);
        return $stmt->fetch();
    }
}
<?php

namespace App\Models;

class Like
{
    public function __construct(int $userId, int $likeableId, string $likeableType)
    {
        $pdo = \App\DB::get();
        // $query = "INSERT INTO posts_likes (post_id, user_id) VALUES (:post_id, :user_id)";
        $query = "INSERT INTO likes (user_id, likeable_id, likeable_type) 
                    VALUES (:user_id, :likeable_id, :likeable_type)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':likeable_type' => $likeableType,
            ':likeable_id' => $likeableId
        ]);
    }

    public static function find(int $userId, int $likeableId, string $likeableType)
    {
        $pdo = \App\DB::get();
        // $query = "SELECT * FROM posts_likes WHERE post_id = :id AND user_id = :user_id";
        $query = "SELECT * FROM likes WHERE 
            user_id = :user_id 
                AND 
            likeable_id = :likeable_id
                AND
            likeable_type = :likeable_type";

        $stmt = $pdo->prepare($query);        
        $stmt->execute([ 
            ':user_id' => $userId,
            ':likeable_id' => $likeableId,
            ':likeable_type' => $likeableType
        ]);
        return $stmt->fetch();
    }

    public static function getByPostId(int $postId)
    {
        $pdo = \App\DB::get();
        $query = "SELECT COUNT(*) FROM posts_likes WHERE 
            likeable_type = 'post' 
            AND 
            likeable_id = :post_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':post_id' => $postId ]);
        return $stmt->fetch();
    }
}
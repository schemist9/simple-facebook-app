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
            SELECT COUNT(likes.id) AS post_likes, posts.id, posts.text, posts.created_at, users.firstname AS author_name, users.surname AS author_surname, users.avatar AS author_avatar FROM posts
                INNER JOIN users 
                    ON posts.user_id = users.id
                LEFT JOIN likes
                    ON posts.id = likes.likeable_id
                    AND
                    likes.likeable_type = 'post'
                WHERE 
                    posts.user_wall_id = :user_wall_id 
                GROUP BY (posts.id, users.id)
                ORDER BY posts.created_at DESC
            ";

            // why not likeable_type in Where?

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':user_wall_id' => $wallId
        ]);
        $posts = $stmt->fetchAll();
        if (empty($posts)) {
            return $posts;
        }
        $postsIds = [];
        foreach ($posts as $key => $value) {
            $postsIds[] = $value['id'];
        }

        $placeholders = str_repeat('?,', count($postsIds) - 1) . '?';


        $commentsQuery = "
            SELECT COUNT(likes.id) as comment_likes, comments.id, comments.commentable_id, comments.text, comments.user_id, comments.created_at, users.firstname AS author_firstname, users.surname AS author_surname, users.avatar AS author_avatar FROM comments
                INNER JOIN users
                    ON users.id = comments.user_id
                LEFT JOIN likes
                    ON likes.likeable_id = comments.id
                    AND
                    likes.likeable_type = 'comment'
                WHERE 
                    comments.commentable_type = 'post' 
                        AND 
                    comments.commentable_id IN ($placeholders)
                GROUP BY comments.id, users.id
        ";

        $stmt = $pdo->prepare($commentsQuery);
        $stmt->execute($postsIds);
        $comments = $stmt->fetchAll();

        if (empty($comments)) {
            return $posts;
        }

        $commentsByPostId = [];

        foreach ($comments as $key => $value) {
            $commentsByPostId[$value['commentable_id']][] = $comments[$key];
        }

        foreach ($posts as $key => $value) {
            $postId = $value['id'];
            
            $posts[$key]['comments'] = $commentsByPostId[$postId] ?? [];
        }

        return $posts;
    }
    
    public function errors(): array
    {
        return $this->errors;
    }
}
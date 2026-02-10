<?php

namespace App\Models;
use App\Models\CommentValidator;

class Comment
{
    private array $errors = [];
    public ?int $id = null;
    public ?int $userId = null;
    public ?string $text = null;
    public ?string $commentableType = null;
    public ?int $commentableId = null;

    public function __construct(array $data)
    {
        $this->userId = $data['user_id'];
        $this->text = $data['text'];
        $this->commentableType = $data['commentable_type'];
        $this->commentableId = $data['commentable_id'];
    }

    public static function find(int $id)
    {
        $pdo = \App\DB::get();
        $query = "SELECT * FROM comments WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':id' => $id ]);
        return $stmt->fetch();
    }

    public function create()
    {
        $errors = CommentValidator::validate([ 'text' => $this->text ]);

        if ($errors) {
            return $errors;
        }

        $pdo = \App\DB::get();
        $query = "INSERT INTO comments (user_id, text, commentable_type, commentable_id)
            VALUES (:user_id, :text, :commentable_type, :commentable_id)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':user_id' => $this->userId,
            ':text' => $this->text,
            ':commentable_type' => $this->commentableType,
            ':commentable_id' => $this->commentableId
        ]);
    }
}
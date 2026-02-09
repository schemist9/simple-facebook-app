<?php

namespace App\Models;

class CommentValidator
{
    public static function validate(array $comment): array
    {
        $errors = [];

        if ($error = self::validateText($comment['text'] ?? '')) {
            $errors['text'] = $error;
        }

        return $errors;
    }

    public static function validateText(string $text): ?string
    {
        return null;
    }
}
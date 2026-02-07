<?php

namespace App\Models;

class PostValidator 
{
    public static function validate(array $user): array
    {
        $errors = [];

        if ($error = self::validateText($post['text'] ?? '')) {
            $errors['text'] = $error;
        }

        return $errors;
    }

    public static function validateText(string $text): ?string
    {
        return null;
    }
}
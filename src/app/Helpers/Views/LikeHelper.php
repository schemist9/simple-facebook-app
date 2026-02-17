<?php

namespace App\Helpers\Views;


class LikeHelper
{
    public function userLikedPost(int $userId, array $post)
    {
        foreach ($post['likes'] as $key => $value) {
            if ((int) $value['user_id'] === $userId) {
                return true;
            }
        }

        return false;
    }

    public function userLikedComment(int $userId, array $comment)
    {
        
        foreach ($comment['likes'] as $key => $value) {
            if ((int) $value['user_id'] === $userId) {
                return true;
            }
        }

        return false;
    }
}
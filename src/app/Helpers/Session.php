<?php

namespace App\Helpers;

class Session
{
    public static function loggedIn() 
    {
        return isset($_SESSION['user_id']);
    }

    public static function currentUser()
    {
        if (!self::loggedIn()) return null;
        return \App\Models\User::find($_SESSION['user_id'])['id'];
    }
}
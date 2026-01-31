<?php

namespace App\Helpers;

class Session
{
    public static function loggedIn() 
    {
        return isset($_SESSION['user_id']);
    }
}
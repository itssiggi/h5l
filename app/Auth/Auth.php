<?php

namespace App\Auth;

use App\Models\User;

class Auth
{
    public function attempt($name, $password) 
    {
        $user = User::where('name', $name)->first();

        var_dump($name);
        
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            $_SESSION['user'] = $user->id;
            return true;
        }

        return false;

    }
}
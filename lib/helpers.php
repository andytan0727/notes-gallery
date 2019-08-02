<?php

namespace NotesGalleryLib\helpers;

use NotesGalleryApp\Models\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use ReallySimpleJWT\Token;

if (!function_exists('generateID')) {
    function generateID(): string
    {
        try {
            $uuid4 = Uuid::uuid4();
            return $uuid4->toString();
        } catch (UnsatisfiedDependencyException $e) {
            echo 'Error in creating unique id: ' . $e->getMessage();
        }
    }
}

if (!function_exists('generateToken')) {
    function generateToken($id)
    {
        $secret = $_ENV['TOKEN_KEY'];
        $expiration = strtotime('+30 days');
        $issuer = 'localhost';

        return Token::create($id, $secret, $expiration, $issuer);
    }
}

if (!function_exists('sanitizeInput')) {
    function sanitizeInput(string $oriStr): string
    {
        return htmlspecialchars($oriStr);
    }
}

if (!function_exists('saveUserTokenToCookie')) {
    function saveUserTokenToCookie(User $user)
    {
        $days = strtotime('+30 days');
        $path = '/';

        setcookie('TOKEN', $user->token, $days, $path);
    }
}

if (!function_exists('saveUserToSession')) {
    function saveUserToSession(User $user)
    {
        $_SESSION['CURRENT_USER_ID'] = $user->id;
        $_SESSION['CURRENT_USER_NAME'] = $user->username;
        $_SESSION['LOGGED_IN'] = true;
    }
}

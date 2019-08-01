<?php
use ReallySimpleJWT\Token;
use NotesGalleryApp\Database\MySqlDB;
use NotesGalleryApp\Repositories\UserRepository;
use function NotesGalleryLib\helpers\saveUserToSession;

// init user if token exists in cookie
if (isset($_COOKIE['TOKEN'])) {
    $token = $_COOKIE['TOKEN'];
    $payload = Token::getPayload($token, $_ENV['TOKEN_KEY']);
    $userRepo = new UserRepository(new MySqlDB());
    $user = $userRepo->findOne($payload['user_id']);
    saveUserToSession($user);
}

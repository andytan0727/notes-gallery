<?php
use ReallySimpleJWT\Token;
use NotesGalleryApp\Database\MySqlDB;
use NotesGalleryApp\Repositories\UserRepository;

$token = $_COOKIE['TOKEN'];

// init user if token exists in cookie
if ($token) {
    $payload = Token::getPayload($token, $_ENV['TOKEN_KEY']);
    $userRepo = new UserRepository(new MySqlDB());
    $user = $userRepo->findOne($payload['user_id']);
    $_SESSION['CURRENT_USER_ID'] = $user->id;
    $_SESSION['CURRENT_USER_NAME'] = $user->username;
}

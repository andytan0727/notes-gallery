<?php

use NotesGalleryApp\Controllers\AuthController;
use NotesGalleryApp\Controllers\HomeController;
use NotesGalleryApp\Controllers\NoteController;
use NotesGalleryApp\Controllers\UserController;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);

    // auth
    $r->addRoute('GET', '/register', [AuthController::class, 'registerView']);
    $r->addRoute('GET', '/login', [AuthController::class, 'loginView']);
    $r->addRoute('POST', '/login', [AuthController::class, 'loginUser']);
    $r->addRoute('POST', '/logout', [AuthController::class, 'logoutUser']);

    // user
    $r->addRoute('POST', '/users/create', [UserController::class, 'create']);
    $r->addRoute('GET', '/users', [UserController::class, 'show']);

    $r->addRoute('GET', '/users/{id}', [UserController::class, 'showOne']);

    // note
    $r->addRoute('POST', '/notes/create', [NoteController::class, 'create']);
    $r->addRoute('GET', '/notes/{id}', [NoteController::class, 'showOne']);
});

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
    $r->addGroup('/users', function (RouteCollector $r) {
        $r->addRoute('GET', '', [UserController::class, 'show']);
        $r->addRoute('GET', '/{id}', [UserController::class, 'showOne']);
        $r->addRoute('POST', '/create', [UserController::class, 'create']);
    });

    // note
    $r->addGroup('/notes', function (RouteCollector $r) {
        $r->addRoute('GET', '/edit', [NoteController::class, 'edit']);
        $r->addRoute('POST', '/create', [NoteController::class, 'create']);
        $r->addRoute('GET', '/{id}', [NoteController::class, 'showOne']);
    });
});

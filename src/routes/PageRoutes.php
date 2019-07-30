<?php

use NotesGalleryApp\Controllers\HomeController;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use NotesGalleryApp\Controllers\UserController;
use NotesGalleryApp\Controllers\NoteController;
use NotesGalleryApp\Controllers\RegistrationController;

return simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/login', function () {
        echo 'hello';
    });

    // user
    $r->addRoute('POST', '/users/create', [UserController::class, 'create']);
    $r->addRoute('GET', '/users', [UserController::class, 'show']);
    $r->addRoute('GET', '/users/{id}', [UserController::class, 'showOne']);

    $r->addRoute('GET', '/register', [RegistrationController::class, 'register']);

    // note
    $r->addRoute('POST', '/notes/create', [NoteController::class, 'create']);
});

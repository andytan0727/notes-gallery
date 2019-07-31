<?php

namespace NotesGalleryApp\Controllers;

class AuthController extends BaseController
{
    public function register()
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $rendered = $this->twig->render('register.html');
        $response->getBody()->write($rendered);

        return $response;
    }
}

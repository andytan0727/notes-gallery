<?php

namespace NotesGalleryApp\Controllers;

class RegistrationController extends BaseController
{
    public function register()
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $rendered = $this->twig->render('user-form.html');
        $response->getBody()->write($rendered);

        return $response;
    }
}

<?php

namespace NotesGalleryApp\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $rendered = $this->twig->render('home.html');
        $response->getBody()->write($rendered);

        return $response;
    }
}

<?php

namespace NotesGalleryApp\Controllers;

use NotesGalleryApp\Views\TwigBuilder;
use Zend\Diactoros\Response;

abstract class BaseController
{
    protected $twig;
    protected $response;

    public function __construct(Response $response, TwigBuilder $twigBuilder)
    {
        $this->response = $response;
        $this->twig = $twigBuilder->getTwig();
    }
}

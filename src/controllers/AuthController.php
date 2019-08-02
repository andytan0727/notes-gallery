<?php

namespace NotesGalleryApp\Controllers;

use NotesGalleryApp\Views\TwigBuilder;
use NotesGalleryApp\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use function NotesGalleryLib\helpers\saveUserTokenToCookie;
use function NotesGalleryLib\helpers\saveUserToSession;

class AuthController extends BaseController
{
    private $userRepo;

    public function __construct(Response $response, TwigBuilder $twigBuilder, UserRepository $userRepo)
    {
        parent::__construct($response, $twigBuilder) ;
        $this->userRepo = $userRepo;
    }

    public function registerView()
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $rendered = $this->twig->render('register.html');
        $response->getBody()->write($rendered);

        return $response;
    }

    public function loginView()
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $rendered = $this->twig->render('login.html');
        $response->getBody()->write($rendered);

        return $response;
    }

    public function loginUser(ServerRequestInterface $request)
    {
        $parsedBody = $request->getParsedBody();

        // sanitize and escape input
        $username = $parsedBody['username'];
        $password = $parsedBody['password'];

        // verify user
        $isUserVerified = $this->userRepo->verifyUser($username, $password);

        // return 401 unauthorized if the user cannot be verified
        if (!$isUserVerified) {
            return $this->response->withStatus(401);
        }

        // save to session and cookie
        $user = $this->userRepo->findOneByUsername($username);
        saveUserTokenToCookie($user);
        saveUserToSession($user);

        return $this->response->withStatus(200);
    }
}

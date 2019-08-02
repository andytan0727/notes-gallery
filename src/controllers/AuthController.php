<?php

namespace NotesGalleryApp\Controllers;

use NotesGalleryApp\Views\TwigBuilder;
use NotesGalleryApp\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\EmptyResponse;
use function NotesGalleryLib\helpers\saveUserTokenToCookie;
use function NotesGalleryLib\helpers\saveUserToSession;
use function NotesGalleryLib\helpers\destroyTokenCookie;

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
        $rendered = $this->twig->render('auth/register.html');
        $response->getBody()->write($rendered);

        return $response;
    }

    public function loginView()
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $rendered = $this->twig->render('auth/login.html');
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

    public function logoutUser(ServerRequestInterface $request)
    {
        $userId = $request->getQueryParams()['id'];

        // return 200 OK if successfully logout user
        if (isset($_SESSION['LOGGED_IN']) && $userId === $_SESSION['CURRENT_USER_ID']) {
            // destroy session and token cookie
            session_destroy();
            destroyTokenCookie();

            return new EmptyResponse(200);
        }

        // else, return 409 Conflict response
        return new EmptyResponse(409);
    }
}

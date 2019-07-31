<?php

namespace NotesGalleryApp\Controllers;

use NotesGalleryApp\Repositories\UserRepository;
use NotesGalleryApp\Models\User;
use NotesGalleryApp\Views\TwigBuilder;
use Psr\Http\Message\ServerRequestInterface;
use zend\Diactoros\Response;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\Response\EmptyResponse;
use function NotesGalleryLib\helpers\generateToken;
use function NotesGalleryLib\helpers\generateID;

class UserController extends BaseController
{
    private $userRepo;

    public function __construct(Response $response, TwigBuilder $twigBuilder, UserRepository $userRepo)
    {
        parent::__construct($response, $twigBuilder);
        $this->userRepo = $userRepo;
    }

    public function create(ServerRequestInterface $serverRequest)
    {
        // get posted data (username & password)
        $parsedBody = $serverRequest->getParsedBody();

        // populate new user model with data
        $user = new User();
        $user->id = generateID();
        $user->username = $parsedBody['username'];
        $user->password = $parsedBody['password'];
        $user->token = generateToken($user->id);

        // save to database
        $result = $this->userRepo->save($user);

        // return error response to client if user exists
        if (!$result) {
            return new TextResponse('Error creating user. Probably user exists', 502);
        }

        // // Set token to cookie if successfully save user to database
        setcookie('TOKEN', $user->token, strtotime('+30 days'), '/');

        // Return 201 created response if successfully created user
        return new EmptyResponse(201, [
            'Location' => ['/']
        ]);
    }

    public function show()
    {
        $allUsers = $this->userRepo->findAll();
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $rendered = $this->twig->render('users.html', ['users' => $allUsers]);
        $response->getBody()->write($rendered);

        return $response;
    }

    public function showOne(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        $userSelected = $this->userRepo->findOne($id);
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $rendered = $this->twig->render('user.html', ['user' => $userSelected]);
        $response->getBody()->write($rendered);

        return $response;
    }
}

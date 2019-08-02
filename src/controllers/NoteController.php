<?php

namespace NotesGalleryApp\Controllers;

use NotesGalleryApp\Models\Note;
use NotesGalleryApp\Repositories\NoteRepository;
use NotesGalleryApp\Views\TwigBuilder;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\EmptyResponse;
use function NotesGalleryLib\helpers\generateID;

class NoteController extends BaseController
{
    private $noteRepo;

    public function __construct(Response $response, TwigBuilder $twigBuilder, NoteRepository  $noteRepo)
    {
        parent::__construct($response, $twigBuilder);
        $this->noteRepo = $noteRepo;
    }

    public function create(ServerRequestInterface $serverRequest)
    {
        $currentUserId = $_SESSION['CURRENT_USER_ID'];

        // user needs to be logged in to save note
        // return 409 conflict for user to resubmit the form after login
        if (!$currentUserId) {
            return new EmptyResponse(409);
        }

        $parsedBody = $serverRequest->getParsedBody();

        $note = new Note();
        $note->id = generateID();
        $note->title = $parsedBody['title'];
        $note->content = $parsedBody['content'];
        $note->description = $parsedBody['description'];
        $note->authorId = $currentUserId;

        $result = $this->noteRepo->save($note);

        // 201 created if everything goes well
        if ($result) {
            return new EmptyResponse(201);
        }

        // status 500 if something unexpected occurs on database or server side
        return new EmptyResponse(500);
    }

    public function edit()
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $rendered = $this->twig->render('notes/editNote.html');
        $response->getBody()->write($rendered);

        return $response;
    }

    public function showOne(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        $note = $this->noteRepo->findOne($id);

        if ($note->authorId !== $_SESSION['CURRENT_USER_ID']) {
            return $this->response->withStatus(401, 'User Unauthorized');
        }

        $rendered = $this->twig->render('notes/note.html', ['note' => $note]);
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write($rendered);

        return $response;
    }
}

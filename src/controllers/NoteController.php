<?php

namespace NotesGalleryApp\Controllers;

use NotesGalleryApp\Repositories\NoteRepository;
use NotesGalleryApp\Views\TwigBuilder;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use NotesGalleryApp\Models\Note;
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

        $parsedBody = $serverRequest->getParsedBody();

        $note = new Note();
        $note->id = generateID();
        $note->title = $parsedBody['title'];
        $note->content = $parsedBody['content'];
        $note->description = $parsedBody['description'];
        $note->authorId = $currentUserId;

        $result = $this->noteRepo->save($note);

        if ($result) {
            $response = $this->response
                          ->withStatus(200)
                          ->withHeader(
                              'Content-Type',
                              'application/json'
                          );
            $response->getBody()->write(json_encode($note));
            return $response;
        }

        return $this->response->withStatus(502);
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

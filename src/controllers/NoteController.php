<?php

namespace NotesGalleryApp\Controllers;

use NotesGalleryApp\Models\Note;
use NotesGalleryApp\Repositories\NoteRepository;
use NotesGalleryApp\Views\TwigBuilder;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;
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
        $rendered = $this->twig->render('notes/editNote.html');

        return new HtmlResponse($rendered);
    }

    public function show(ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();

        // get note by noteId
        if (isset($queryParams['id'])) {
            $noteId = $queryParams['id'];
            return  $this->showOneByNoteId($noteId);
        }

        // get notes by authorId
        elseif (isset($queryParams['authorId'])) {
            $authorId = $queryParams['authorId'];
            return $this->showByAuthorId($authorId);
        }

        // if no query params supplied
        elseif (empty($queryParams)) {
            return $this->showAllNotes();
        }
    }

    private function showOneByNoteId(string $noteId)
    {
        $note = $this->noteRepo->findOne($noteId);

        $rendered = $this->twig->render('notes/note.html', ['note' => $note]);
        return new HtmlResponse($rendered);
    }

    private function showByAuthorId(string $authorId)
    {
        $notes = $this->noteRepo->findAllByAuthorId($authorId);

        // get notes' author name to display on view
        $noteAuthor = $this->noteRepo->findNoteAuthorById($authorId);

        $rendered = $this->twig->render('notes/noteCards.html', ['notes' => $notes, 'author' => $noteAuthor]);
        return new HtmlResponse($rendered);
    }

    private function showAllNotes()
    {
        $notesWithUsername = $this->noteRepo->findNotesWithUsername();

        $rendered = $this->twig->render('notes/noteCards.html', ['notes' => $notesWithUsername]);
        return new HtmlResponse($rendered);
    }
}

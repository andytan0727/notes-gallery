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
use function NotesGalleryLib\helpers\getCurrentUserId;

class NoteController extends BaseController
{
    private $noteRepo;

    public function __construct(Response $response, TwigBuilder $twigBuilder, NoteRepository  $noteRepo)
    {
        parent::__construct($response, $twigBuilder);
        $this->noteRepo = $noteRepo;
    }

    /**
     * Create note controller
     *
     * Create a new note in database based on contents provided by client
     *
     * @param ServerRequestInterface $serverRequest
     * @return EmptyResponse
     */
    public function create(ServerRequestInterface $request): EmptyResponse
    {
        $parsedBody = $request->getParsedBody();

        $note = new Note();
        $note->id = generateID();
        $note->title = $parsedBody['title'];
        $note->content = $parsedBody['content'];
        $note->description = $parsedBody['description'];
        $note->authorId = getCurrentUserId();

        $result = $this->noteRepo->save($note);

        // 201 created if everything goes well
        if ($result) {
            return new EmptyResponse(201);
        }

        // status 500 if something unexpected occurs on database or server side
        return new EmptyResponse(500);
    }

    /**
     * Render view to edit note, then create or update note depending on the
     * query params provided
     *
     * @param ServerRequestInterface $request
     * @return HtmlResponse
     *
     */
    public function edit(ServerRequestInterface $request): HtmlResponse
    {
        $queryParams = $request->getQueryParams();

        // render view to update note by id if id query param is provided
        if (isset($queryParams['id'])) {
            $noteId = $queryParams['id'];
            return $this->updateNoteView($noteId);
        }

        // render default view to create new note if no query params supplied
        return $this->createNoteView();
    }

    /**
     * Note update controller
     *
     * Update note specified by noteId
     *
     * @param ServerRequestInterface $request
     * @return EmptyResponse
     */
    public function update(ServerRequestInterface $request): EmptyResponse
    {
        $queryParams = $request->getQueryParams();

        // return 400 bad request if client failed to provide query params needed
        if (!isset($queryParams['noteId'])) {
            return new EmptyResponse(400);
        }

        // process only if noteId and authorId are supplied as query params
        // authorId is mainly used for auth to make change (update)
        $noteId = $queryParams['noteId'];
        $authorId = $queryParams['authorId'];
        parse_str($request->getBody()->getContents(), $reqBody);

        $note = new Note();
        $note->id = $noteId;
        $note->title = $reqBody['title'];
        $note->content = $reqBody['content'];
        $note->description = $reqBody['description'];
        $note->authorId = $authorId;

        $result = $this->noteRepo->updateOne($note);

        // 200 OK if operation is successfully processed on database
        if ($result) {
            return new EmptyResponse(200);
        }

        // 500 Internal Server Error if database operation failed
        return new EmptyResponse(500);
    }

    /**
     * Show note view controller
     *
     * Render note views based on the query params provided
     *
     * @param ServerRequestInterface $request
     */
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

    /**
     * Render a twig view to update
     *
     * @param string $noteId
     * @return HtmlResponse|EmptyResponse
     */
    private function updateNoteView(string $noteId)
    {
        $note = $this->noteRepo->findOne($noteId);

        if (!$note) {
            // replace with custom404 not found page in the future
            return new EmptyResponse(404);
        }

        $rendered = $this->twig->render('notes/editNote.html', ['method' => 'PUT', 'action' => "update?noteId=$noteId&authorId=$note->authorId", 'note' => $note]);

        return new HtmlResponse($rendered);
    }

    /**
     * Create note view
     *
     * @return HtmlResponse
     */
    private function createNoteView(): HtmlResponse
    {
        $currentUserId = getCurrentUserId();

        $rendered = $this->twig->render('notes/editNote.html', ['method' => 'POST', 'action' => "create?authorId=$currentUserId"]);

        return new HtmlResponse($rendered);
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

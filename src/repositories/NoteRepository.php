<?php

namespace NotesGalleryApp\Repositories;

use NotesGalleryApp\Database\MySqlDB;
use NotesGalleryApp\Interfaces\NoteRepositoryInterface;
use NotesGalleryApp\Models\Note;
use stdClass;
use function NotesGalleryLib\helpers\sanitizeInput;

class NoteRepository implements NoteRepositoryInterface
{
    private $db;

    public function __construct(MySqlDB $db)
    {
        $this->db = $db;
    }

    /**
     * Find one note with noteId supplied
     *
     * @param string $id
     * @return Note
     */
    public function findOne(string $id): Note
    {
        $note = new Note();
        $result = $this->db->findOneById($id, 'notes');
        $note->id = $id;
        $note->title = $result['title'];
        $note->content = $result['content'];
        $note->description = $result['description'];
        $note->authorId = $result['authorId'];

        return $note;
    }

    /**
     * Find all notes regardless of author
     * @return array|null
     */
    public function findAll()
    {
        $result = $this->db->findAll('notes');

        if ($result !== false) {
            $notes = [];

            foreach ($result as $row) {
                $note = new Note();
                $note->id = $row[0];
                $note->title = $row[1];
                $note->content = $row[2];
                $note->description = $row[3];
                $note->authorId = $row[4];
                $notes[] = $note;
            }

            return $notes;
        }

        return null;
    }

    /**
     * Find all notes written by the user with authorId supplied
     *
     * @param string $authorId
     * @return array|null
     */
    public function findAllByAuthorId(string $authorId)
    {
        $result = $this->db->query('SELECT * FROM notes WHERE authorId = ' . "'$authorId'");

        if ($result->num_rows === 0) {
            return null;
        }

        $notes = [];
        foreach ($result->fetch_all() as $row) {
            $note = new Note();
            $note->id = $row[0];
            $note->title = $row[1];
            $note->content = $row[2];
            $note->description = $row[3];
            $note->authorId = $row[4];
            $notes[] = $note;
        }

        return $notes;
    }

    /**
     * Find table joined by user's id and note's authorId
     *
     * @param string $authorId
     * @return array|null
     */
    public function findNotesWithUsername()
    {
        $result = $this->db->query('SELECT users.username, notes.title, notes.content, notes.description FROM users JOIN notes ON users.id = notes.authorId');

        if ($result->num_rows === 0) {
            return null;
        }

        $notesWithUsername = [];
        foreach ($result->fetch_all() as $row) {
            $note = new stdClass();
            $note->username = $row[0];
            $note->title = $row[1];
            $note->content = $row[2];
            $note->description = $row[3];
            $notesWithUsername[] = $note;
        }

        return $notesWithUsername;
    }

    /**
     * Find note's author by using author id
     *
     * @param string $authorId
     * @return array|null
     */
    public function findNoteAuthorById(string $authorId)
    {
        $result = $this->db->query('SELECT username from users WHERE id = ' . "'$authorId'");

        if ($result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    public function updateOne(Note $note)
    {
    }

    public function deleteOne(string $id)
    {
    }

    public function deleteAll()
    {
    }

    /**
     * Save note to database
     *
     * @param Note $note
     */
    public function save(Note $note)
    {
        if (!isset($note->authorId)) {
            throw new \Exception('Author id is not defined');
        }

        $noteId = $note->id;
        $title = $this->db->escape(sanitizeInput($note->title));
        $content = $this->db->escape(sanitizeInput($note->content));
        $description = $this->db->escape(sanitizeInput($note->description));
        $authorId = $note->authorId;

        return $this->db->query("INSERT INTO notes VALUES ('$noteId', '$title', '$content', '$description', '$authorId')");
    }
}

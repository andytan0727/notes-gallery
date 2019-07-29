<?php

namespace NotesGalleryApp\Repositories;

use NotesGalleryApp\Interfaces\NoteRepositoryInterface;
use NotesGalleryApp\Database\MySqlDB;
use NotesGalleryApp\Models\Note;
use function NotesGalleryLib\helpers\generateID;
use function NotesGalleryLib\helpers\sanitizeInput;

class NoteRepository implements NoteRepositoryInterface
{
    private $db;

    public function __construct(MySqlDB $db)
    {
        $this->db = $db;
    }

    public function findOne(string $id): Note
    {
        $note = new Note();

        return $note;
    }

    public function findAll(): array
    {
        $notes = [];

        return $notes;
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

    public function save(Note $note)
    {
        // escape
        $note->id = $this->db->escape(generateID());
        $note->title = $this->db->escape($note->title);
        $note->content = $this->db->escape($note->content);
        $note->description = $this->db->escape($note->description);
        $note->authorId = $this->db->escape($note->authorId);

        // sanitize
        $noteId = sanitizeInput($note->id);
        $title = sanitizeInput($note->title);
        $content = sanitizeInput($note->content);
        $description = sanitizeInput($note->description);
        $authorId = sanitizeInput($note->authorId);
    }
}

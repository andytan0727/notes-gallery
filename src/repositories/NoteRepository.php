<?php

namespace NotesGalleryApp\Repositories;

use NotesGalleryApp\Database\MySqlDB;
use NotesGalleryApp\Interfaces\NoteRepositoryInterface;
use NotesGalleryApp\Models\Note;
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
        $result = $this->db->findOneById($id, 'notes');
        $note->id = $id;
        $note->title = $result['title'];
        $note->content = $result['content'];
        $note->description = $result['description'];
        $note->authorId = $result['authorId'];

        return $note;
    }

    public function findAll(): array
    {
        return $this->db->findAll('notes');
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

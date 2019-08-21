<?php

namespace NotesGalleryApp\Interfaces;

use NotesGalleryApp\Models\Note;

interface NoteRepositoryInterface
{
    public function findOne(string $id);

    public function findAll();

    public function findAllByAuthorId(string $authorId);

    public function findNotesWithUsername();

    public function findNoteAuthorById(string $authorId);

    public function updateOne(Note $note): bool;

    public function deleteOne(Note $note): bool;

    public function save(Note $note);
}

<?php

namespace NotesGalleryApp\Interfaces;

use NotesGalleryApp\Models\Note;

interface NoteRepositoryInterface
{
    public function findOne(string $id): Note;

    public function findAll(): array;

    public function updateOne(Note $note);

    public function deleteOne(string $id);

    public function deleteAll();

    public function save(Note $note);
}

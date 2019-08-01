<?php

namespace NotesGalleryApp\Interfaces;

interface DatabaseInterface
{
    public function findOneById(string $id, string $table);

    public function findAll(string $table): array;

    public function escape(string $oriStr): string;

    public function query(string $sql);
}

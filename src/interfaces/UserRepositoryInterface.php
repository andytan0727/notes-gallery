<?php

namespace NotesGalleryApp\Interfaces;

use NotesGalleryApp\Models\User;

interface UserRepositoryInterface
{
    public function findOne(string $id): User;

    public function findOneByUsername(string $username);

    public function findAll(): array;

    public function verifyUser(string $username, string $password): bool;

    public function save(User $user);
}

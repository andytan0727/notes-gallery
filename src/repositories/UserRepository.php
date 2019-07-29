<?php

namespace NotesGalleryApp\Repositories;

use NotesGalleryApp\Interfaces\UserRepositoryInterface;
use NotesGalleryApp\Database\MySqlDB;
use NotesGalleryApp\Models\User;
use function NotesGalleryLib\helpers\sanitizeInput;

class UserRepository implements UserRepositoryInterface
{
    private $db;

    public function __construct(MySqlDB $db)
    {
        $this->db = $db;
    }

    public function findOne(string $id): User
    {
        $result = $this->db->findOneById($id, 'users');
        $user = new User();
        $user->id = $result['id'];
        $user->username = $result['username'];
        $user->password = $result['password'];
        return $user;
    }

    public function findAll(): array
    {
        $users = [];
        $results = $this->db->findAll('users');
        foreach ($results as $row) {
            $user = new User();
            $user->id = $row[0];
            $user->username = $row[1];
            $user->password = $row[2];
            $users[] = $user;
        }

        return $users;
    }

    public function updateOne(User $user)
    {
    }

    public function deleteOne(string $id)
    {
    }

    public function deleteAll()
    {
    }

    public function save(User $user)
    {
        // escape
        $id = $this->db->escape($user->id);
        $username = $this->db->escape($user->username);
        $password = $this->db->escape($user->password);
        $token = $this->db->escape($user->token);

        // sanitize user input
        $username = sanitizeInput($username);
        $password = sanitizeInput($password);

        // hashing password
        $password = password_hash($password, PASSWORD_BCRYPT);

        // persist to database
        $mysqli = $this->db->getDBInstance();

        $res = $mysqli->query("INSERT INTO users VALUES ('$id', '$username', '$password', '$token')");

        if (!$res) {
            throw new \Exception('Error saving user');
        }
    }
}

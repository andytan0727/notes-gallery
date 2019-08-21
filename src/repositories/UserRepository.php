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
        $user->token = $result['token'];
        $user->avatarUrl = $result['avatarUrl'];
        return $user;
    }

    /**
     * Find one user by username. Return null if unable to find
     *
     * @param string $username
     * @return User|null
     */
    public function findOneByUsername(string $username)
    {
        $result = $this->db->query('SELECT * FROM users WHERE username = ' . "'$username'");

        if ($result->num_rows === 0) {
            return null;
        }

        $userFromDB = $result->fetch_assoc();
        $user = new User();
        $user->id = $userFromDB['id'];
        $user->username = $userFromDB['username'];
        $user->password = $userFromDB['password'];
        $user->token = $userFromDB['token'];
        $user->avatarUrl = $userFromDB['avatarUrl'];
        return $user;
    }

    /**
     * Find all users
     *
     * @return array
     */
    public function findAll(): array
    {
        $users = [];
        $results = $this->db->findAll('users');
        foreach ($results as $row) {
            $user = new User();
            $user->id = $row[0];
            $user->username = $row[1];
            $user->password = $row[2];
            $user->token = $row[3];
            $user->avatarUrl = $row[4];
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Verify user password
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function verifyUser(string $username, string $password): bool
    {
        // sanitize and escape input
        $username = sanitizeInput($this->db->escape($username));
        $password = sanitizeInput($this->db->escape($password));

        // query hashed password in database
        $query = $this->db->query('SELECT password FROM users WHERE username = ' . "'$username'");

        $dbPassword = $query->fetch_assoc()['password'];

        // return verified result
        return password_verify($password, $dbPassword);
    }

    public function save(User $user)
    {
        // escape
        $id = $this->db->escape($user->id);
        $username = $this->db->escape($user->username);
        $password = $this->db->escape($user->password);
        $token = $this->db->escape($user->token);
        $avatarUrl = $this->db->escape($user->avatarUrl);

        // sanitize user input
        $username = sanitizeInput($username);
        $password = sanitizeInput($password);

        // hashing password
        $password = password_hash($password, PASSWORD_BCRYPT);

        // check if user exists with the same username
        $userExists = $this->db->query('SELECT * FROM users WHERE username = ' . "'$username'");

        // return if exists
        if ($userExists->num_rows !== 0) {
            return false;
        }

        // save to database if user not exists before
        return $this->db->query("INSERT INTO users VALUES ('$id', '$username', '$password', '$token', '$avatarUrl')");
    }
}

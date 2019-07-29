<?php

namespace NotesGalleryApp\Database;

use NotesGalleryApp\Interfaces\DatabaseInterface;
use PDO;
use mysqli;

class MySqlDB implements DatabaseInterface
{
    /**
     * Mysql DB instance
     *
     * @var mysqli
     */
    private $db;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $dbName = $_ENV['DB_NAME'];

        $this->db = new mysqli($host, $username, $password, $dbName);

        if ($this->db->connect_errno) {
            echo 'Failed to connect to MYSQL: ' . $this->db->connect_error;
        }
    }

    public function findOneById(string $id, string $table)
    {
        $escapedId = $this->db->real_escape_string($id);
        $escapedTable = $this->db->real_escape_string($table);
        $result = $this->db->query('SELECT * FROM ' . $escapedTable . ' WHERE id = ' . "'$escapedId'");
        return $result->fetch_assoc();
    }

    public function findAll(string $table)
    {
        $escapedTable = $this->db->real_escape_string($table);
        $result = $this->db->query('SELECT * FROM ' . $escapedTable);
        return $result->fetch_all();
    }

    public function escape(string $oriStr): string
    {
        return $this->db->real_escape_string($oriStr);
    }

    public function getDBInstance()
    {
        return $this->db;
    }
}

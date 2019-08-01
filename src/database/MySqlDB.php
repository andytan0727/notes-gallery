<?php

namespace NotesGalleryApp\Database;

use NotesGalleryApp\Interfaces\DatabaseInterface;
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

    /**
     * Find one entry of a table
     *
     * @param string $id Id of the entry
     * @param string $table Table to be queried
     * @return array|null
     */
    public function findOneById(string $id, string $table)
    {
        $escapedId = $this->db->real_escape_string($id);
        $escapedTable = $this->db->real_escape_string($table);
        $result = $this->db->query('SELECT * FROM ' . $escapedTable . ' WHERE id = ' . "'$escapedId'");
        return $result->fetch_assoc();
    }

    /**
     * Find all entries of a table
     *
     * @param string $table Table to be queried
     * @return array
     */
    public function findAll(string $table): array
    {
        $escapedTable = $this->db->real_escape_string($table);

        $result = $this->db->query('SELECT * FROM ' . $escapedTable);

        return $result ? $result->fetch_all() : [];
    }

    /**
     * Escape supplied string
     *
     * @param string $oriStr String to be escaped
     * @return string
     */
    public function escape(string $oriStr): string
    {
        return $this->db->real_escape_string($oriStr);
    }

    /**
     * Generic query using mysqli
     *
     * @param string $sql Query statement to be passed to mysqli query
     */
    public function query(string $sql)
    {
        return $this->db->query($sql);
    }
}

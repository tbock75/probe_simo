<?php
namespace App\Services;

use PDO;
use PDOException;

class DatabaseService
{
    /**
     * @var string
     */
    private $host = 'localhost';

    /**
     * @var string
     */
    private $dbname = 'guestbook_db';

    /**
     * @var string
     */
    private $username = 'root';

    /**
     * @var string
     */
    private $password = '';

    /**
     * @var
     */
    private $conn;

    /**
     * Creates a connection to the database
     *
     * @return PDO|void
     */
    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            echo "Verbindungsfehler: " . $e->getMessage();
        }
    }

}
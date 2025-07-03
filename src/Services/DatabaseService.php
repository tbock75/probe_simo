<?php
namespace App\Services;

use PDO;
use PDOException;

class DatabaseService
{
    private $host = 'localhost';
    private $dbname = 'guestbook_db';
    private $username = 'root';
    private $password = '';
    private $conn;

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
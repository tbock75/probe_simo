<?php

namespace App\Model;

use \PDO;
use \Exception;

class Guestbook {
    private $conn;

    private string $errorMessage = '';

    private $requiredFields = ['name', 'email', 'message'];
    public function __construct($db) {
        $this->conn = $db;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    private function checkRequiredFields($requestData) {
        try {
            foreach ($requestData as $key => $field) {
                if(in_array($key, $this->requiredFields) && empty($field)) {
                    $this->setErrorMessage('Field: ' . $key . ' ist erforderlich!');
                }
            }
            return true;
        } catch(Exception $e) {
            return false;
        }

    }

    public function addEntry($requestData): bool
    {
        if($this->checkRequiredFields($requestData)) {
            $query = "
            INSERT INTO 
                guestbook (name, email, message) 
            VALUES 
                (:name, :email, :message);
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':name', $requestData['name']);
            $stmt->bindParam(':email', $requestData['email']);
            $stmt->bindParam(':message', $requestData['message']);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } else {
            return false;
        }

    }

    public function getEntries(): array|bool
    {
        try {
            $query = "
            SELECT * 
                FROM guestbook 
                    ORDER BY created_at DESC
        ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteEntry($id): bool
    {
        if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
            $query = "delete from guestbook where id = :id";
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                return true;
            } catch (Exception $e) {
                $this->errorMessage = $e->getMessage();
                return false;
            }
        } else {
            $this->errorMessage = 'Sie sind nicht eingeloggt. Zugriff verweigert.';
            return false;
        }
    }
}

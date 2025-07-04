<?php

namespace App\Model;

use \PDO;
use \Exception;

class Guestbook {
    /**
     * @var
     */
    private $conn;

    /**
     * @var string
     */
    private string $errorMessage = '';

    /**
     * @var string[]
     */
    private $requiredFields = ['name', 'email', 'message'];

    /**
     * @param $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Returns the generated error message
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * Sets the error message
     *
     * @param string $errorMessage
     * @return void
     */
    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Checks all required fields
     *
     * @param $requestData
     * @return bool
     */
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

    /**
     * Adds an entry after verifying the data
     *
     * @param $requestData
     * @return bool
     */
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

    /**
     * Returns all guestbook entries
     *
     * @return array|bool
     */
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

    /**
     * @param $id
     * @return bool
     */
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

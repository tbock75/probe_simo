<?php

namespace App\Model;

use \PDO;
use \Exception;

class Guestbook {
    private $conn;

    private $requiredFields = ['name', 'email', 'message'];
    public function __construct($db) {
        $this->conn = $db;
    }

    private function checkRequiredFields($requestData) {
        try {
            foreach ($requestData as $key => $field) {
                if(in_array($key, $this->requiredFields) && empty($field)) {
                    throw new Exception('Field: ' . $key . ' ist erforderlich!');
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
}

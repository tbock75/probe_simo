<?php

namespace App\Model;
class Guestbook {
    private $conn;

    private $requiredFields = ['name', 'email', 'message'];
    public function __construct($db) {
        $this->conn = $db;
    }

    private function checkRequiredFields($requestData) {
        try {
            foreach ($requestData as $key => $field) {
                if(in_array($key, $this->requiredFields) && empty($requestData[$field])) {
                    throw new \Exception('Field: ' . $key . ' ist erforderlich!');
                }
            }
            return true;
        } catch(\Exception $e) {
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

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } else {
            return false;
        }

    }

    public function getEntries(): array
    {
        $query = "
            SELECT * 
                FROM guestbook 
                    ORDER BY created_at DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

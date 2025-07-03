<?php

namespace App\Model;

class Admin
{
    private $conn;
    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function verifyAdmin($username, $password)
    {
        $query = "
        SELECT * 
            FROM admin 
                WHERE username = :username AND password = :password
        ";
        $stmt = $this->conn->prepare($query);

        $hashedPassword = hash('sha256', $password);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();
        $admin = $stmt->fetch();

        if($admin) {
            $this->createAdminSession($admin['username'], $admin['email']);
            return true;
        }
        return false;
    }



    private function createAdminSession($username, $email)
    {
        $_SESSION['is_admin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
    }
}
<?php
require_once __DIR__ . '/../Services/DatabaseService.php';

use App\Services\DatabaseService;

try {
    $createGuestbook = <<<EOT
    CREATE TABLE guestbook (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
EOT;

    $database = new DatabaseService();

    $database->connect()->exec($createGuestbook);
} catch (PDOException $e) {
    echo "Verbindungsfehler: " . $e->getMessage();
}


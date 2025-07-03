<?php

require_once __DIR__ . '/../Services/DatabaseService.php';
require_once __DIR__ . '/../Model/Guestbook.php';
require_once __DIR__ . '/../Model/Admin.php';

use App\Services\DatabaseService;
use App\Model\Admin;
use App\Model\Guestbook;

    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $databaseService = new DatabaseService();
        $db = $databaseService->connect();
        $adminObject = new Admin($db);

        $adminObject->verifyAdmin($_POST['username'], $_POST['password']);
    }

    if(isset($_GET['delete'])) {
        $databaseService = new DatabaseService();
        $db = $databaseService->connect();

        $guestbook = new Guestbook($db);

        if ($guestbook->deleteEntry($_GET['delete'])) {
            $success = "Eintrag wurde erfolgreich gelöscht!";
        } else {
            $error = "Es gab einen Fehler beim Löschen des Eintrags.<br />";
            $error .= "Fehler: " . $guestbook->getErrorMessage();
        }
    }

    if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        $databaseService = new DatabaseService();
        $db = $databaseService->connect();
        $guestbook = new Guestbook($db);

        $entries = $guestbook->getEntries();
    }


?>
<!DOCTYPE html>
<html>
<head>
    <title>Gästebuch</title>
    <style>
        .entry {
    border: 1px solid #ccc;
            margin: 10px 0;
            padding: 10px;
        }
        .form-group {
    margin: 10px 0;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>
<body>

    <div class="container p-2">

        <?php if (isset($success)): ?>
            <div style="color: green;"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div style="color: red;"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>

        <h1>Admin</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Admin:</h5>
                <?php foreach ($entries as $entry): ?>
                    <div class="entry">
                        <strong><?php echo htmlspecialchars($entry['name']); ?></strong>
                        <p>
                            <?php echo nl2br(htmlspecialchars($entry['message'])); ?></p>
                        <small>
                            Email: <?php echo htmlspecialchars($entry['email']); ?><br>
                            Datum: <?php echo htmlspecialchars($entry['created_at']); ?>
                        </small>

                        <small>
                            <a href="?delete=<?php echo $entry['id']; ?>">del</a>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <h1>Login:</h1>
        <div class="card-body">
            <h5 class="card-title">Admin:</h5>
            <p class="card-text">
                <form method="post">
                    <div class="form-group">
                        <label for="username">Benutzername:</label>
                        <input type="text" name="username" class="form-control" id="username" placeholder="Benutzername">
                    </div>
                    <div class="form-group">
                        <label for="password">Passwort:</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="">
                    </div>
                    <button type="submit">Login</button>
                </form>
            </p>
        </div>
    <?php endif; ?>


        <div class="row ml-2 d-inline p-absolute" style="right: 200px;">
            <a href="/" class="btn btn-primary">Homepage</a>
        </div>
    </div>
</body>
</html>
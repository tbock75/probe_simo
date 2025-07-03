<?php
    require_once __DIR__ . '/../Services/DatabaseService.php';
    require_once __DIR__ . '/../Model/Guestbook.php';
    require_once __DIR__ . '/../Handlers/RequestHandler.php';

    use App\Services\DatabaseService;
    use App\Model\Guestbook;
    use App\Handlers\RequestHandler;

    session_start();

    $database = new DatabaseService();
    $db = $database->connect();
    $guestbook = new Guestbook($db);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $requestHandler = new RequestHandler();
        $requestHandler->sanitize($_POST);
        $requestData = $requestHandler->all();
        if ($guestbook->addEntry($requestData)) {
            $success = "Eintrag wurde erfolgreich hinzugefügt!";
        } else {
            $error = "Es gab einen Fehler beim Hinzufügen des Eintrags.<br />";
            $error .= "Fehler: " . $guestbook->getErrorMessage();
        }
    }

    $entries = $guestbook->getEntries();
?>

<!DOCTYPE html>
<html lang="de">
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
    <h1>Gästebuch</h1>

<?php if (isset($success)): ?>
    <div style="color: green;"><?php echo $success; ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div style="color: red;"><?php echo $error; ?></div>
<?php endif; ?>
    <div class="card p-2">
        <form method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input id="name" type="text" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">E-Mail:</label>
                <input id="email" type="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="message">Nachricht:</label>
                <textarea id="message" name="message" required></textarea>
            </div>

            <button type="submit">Eintrag hinzufügen</button>
        </form>
    </div>

    <div class="card p-2">
<h2>Einträge</h2>
<?php if(count($entries) === 0): ?>
    <div class="entry">
        <strong>Keine Einträge vorhanden.</strong>
    </div>
<?php else: ?>
    <?php foreach ($entries as $entry): ?>
        <div class="entry">
            <strong><?php echo htmlspecialchars($entry['name']); ?></strong>
            <p>
                <?php echo nl2br(htmlspecialchars($entry['message'])); ?></p>
            <small>
                Email: <?php echo htmlspecialchars($entry['email']); ?><br>
                Datum: <?php echo htmlspecialchars($entry['created_at']); ?>
            </small>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
            <div class="row ml-2 d-inline p-absolute" style="right: 200px;">
                <a href="admin.php" class="btn btn-primary">Admin</a>
            </div>
    </div>
</div>
</body>
</html>

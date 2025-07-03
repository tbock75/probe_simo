<?php
    require_once __DIR__ . '/src/Services/DatabaseService.php';
    require_once __DIR__ . './src/Model/Guestbook.php';

    use App\Services\DatabaseService;
    use App\Model\Guestbook;

    $database = new DatabaseService();
    $db = $database->connect();
    $guestbook = new Guestbook($db);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($guestbook->addEntry($_POST)) {
            $success = "Eintrag wurde erfolgreich hinzugefügt!";
        } else {
            $error = "Es gab einen Fehler beim Hinzufügen des Eintrags.<br />";
            $error .= "Fehler: " . error_get_last()['message'];
        }
    }

    $entries = $guestbook->getEntries();
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
</head>
<body>
<h1>Gästebuch</h1>

<?php if (isset($success)): ?>
    <div style="color: green;"><?php echo $success; ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div style="color: red;"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post">
    <div class="form-group">
        <label>Name:</label>
        <input type="text" name="name" required>
    </div>

    <div class="form-group">
        <label>E-Mail:</label>
        <input type="email" name="email" required>
    </div>

    <div class="form-group">
        <label>Nachricht:</label>
        <textarea name="message" required></textarea>
    </div>

    <button type="submit">Eintrag hinzufügen</button>
</form>

<h2>Einträge</h2>
<?php foreach ($entries as $entry): ?>
    <div class="entry">
        <strong><?php echo $entry['name']; ?></strong>
        <p><?php echo $entry['message']; ?></p>
        <small>
            Email: <?php echo $entry['email']; ?><br>
            Datum: <?php echo $entry['created_at']; ?>
        </small>
    </div>
<?php endforeach; ?>
</body>
</html>

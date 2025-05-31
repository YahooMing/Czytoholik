<?php
session_start();
require_once "conn.php";

// Sprawdzenie, czy użytkownik to pracownik
if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'pracownik') {
    header("Location: index.php");
    exit();
}

// Obsługa usuwania książki
$komunikat = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_ksiazki'])) {
    $id = $_POST['id_ksiazki'];

    // Sprawdź, czy są powiązane egzemplarze
    $stmt = $conn->prepare("SELECT COUNT(*) FROM egzemplarze WHERE k_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($liczba);
    $stmt->fetch();
    $stmt->close();

    if ($liczba > 0) {
        $komunikat = "Nie można usunąć książki, ponieważ istnieją powiązane egzemplarze.";
    } else {
        $stmt = $conn->prepare("DELETE FROM ksiazki WHERE id_ksiazki = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $komunikat = "Książka została usunięta.";
    }
}

// Pobierz wszystkie książki
$ksiazki = $conn->query("SELECT * FROM ksiazki");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Usuń książkę</title>
    <style>
        body { font-family: Arial; background-color: #f3f3f3; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        h2 { text-align: center; }
        form { margin-top: 20px; display: flex; flex-direction: column; gap: 10px; }
        select, button { padding: 10px; font-size: 16px; }
        .info { text-align: center; color: green; font-weight: bold; }
        .error { text-align: center; color: red; font-weight: bold; }
        .back { position: absolute; top: 20px; right: 20px; }
    </style>
</head>
<body>
    <a class="back" href="menu.php">🔙 Wróć</a>
    <div class="container">
        <h2>Usuń książkę</h2>

        <?php if ($komunikat): ?>
            <p class="<?= strpos($komunikat, 'nie można') !== false ? 'error' : 'info' ?>">
                <?= $komunikat ?>
            </p>
        <?php endif; ?>

        <form method="post">
            <label for="id_ksiazki">Wybierz książkę do usunięcia:</label>
            <select name="id_ksiazki" required>
                <option value="">-- wybierz książkę --</option>
                <?php while ($k = $ksiazki->fetch_assoc()): ?>
                    <option value="<?= $k['id_ksiazki'] ?>">
                        <?= htmlspecialchars($k['tytul']) ?> (<?= $k['autor'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Usuń książkę</button>
        </form>
    </div>
</body>
</html>

<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'pracownik') {
    header("Location: index.php");
    exit();
}

$komunikat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $k_id = $_POST['k_id'];
    $stan = $_POST['stan'];
    $egzemplarz = $_POST['egzemplarz'];

    if ($k_id && $stan && $egzemplarz) {
        $stmt = $conn->prepare("INSERT INTO egzemplarze (k_id, stan, egzemplarz) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $k_id, $stan, $egzemplarz);
        if ($stmt->execute()) {
            $komunikat = "Dodano egzemplarz książki.";
        } else {
            $komunikat = "Błąd podczas dodawania egzemplarza.";
        }
        $stmt->close();
    } else {
        $komunikat = "Wszystkie pola są wymagane.";
    }
}

$ksiazki = $conn->query("SELECT id_ksiazki, tytul, autor FROM ksiazki");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj egzemplarz książki</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; padding: 20px; }
        .container { background: white; padding: 20px; max-width: 600px; margin: auto; border-radius: 10px; }
        h2 { text-align: center; }
        form { display: flex; flex-direction: column; gap: 15px; }
        input, select, button { padding: 10px; font-size: 16px; }
        .info { color: green; text-align: center; }
        .back { position: absolute; top: 20px; right: 20px; }
    </style>
</head>
<body>
    <a class="back" href="menu.php">🔙 Wróć</a>
    <div class="container">
        <h2>Dodaj egzemplarz</h2>

        <?php if ($komunikat): ?>
            <p class="info"><?= $komunikat ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="k_id">Książka:</label>
            <select name="k_id" required>
                <option value="">-- wybierz książkę --</option>
                <?php while ($row = $ksiazki->fetch_assoc()): ?>
                    <option value="<?= $row['id_ksiazki'] ?>">
                        <?= htmlspecialchars($row['tytul']) ?> (<?= htmlspecialchars($row['autor']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="stan">Stan egzemplarza:</label>
            <select name="stan" required>
                <option value="nowy">nowy</option>
                <option value="dobry">dobry</option>
                <option value="zużyty">zużyty</option>
            </select>

            <label for="egzemplarz">Kod egzemplarza (np. A-001):</label>
            <input type="text" name="egzemplarz" required>

            <button type="submit">Dodaj egzemplarz</button>
        </form>
    </div>
</body>
</html>

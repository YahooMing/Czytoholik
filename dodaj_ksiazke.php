<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'pracownik') {
    header("Location: index.php");
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tytul = $_POST['tytul'];
    $autor = $_POST['autor'];
    $wydawnictwo = $_POST['wydawnictwo'];
    $rok_wydania = intval($_POST['rok_wydania']);
    $kategoria = $_POST['kategoria'];

    if ($tytul && $autor && $wydawnictwo && $rok_wydania && $kategoria) {
        $stmt = $conn->prepare("INSERT INTO ksiazki (tytul, autor, wydawnictwo, rok_wydania, kategoria) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $tytul, $autor, $wydawnictwo, $rok_wydania, $kategoria);
        $stmt->execute();
        $stmt->close();

        $msg = "Książka została dodana pomyślnie!";
    } else {
        $msg = "Wszystkie pola są wymagane.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj książkę</title>
    <style>
        body { font-family: Arial; background-color: #f0f0f0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border-radius: 5px; border: 1px solid #ccc; }
        .msg { color: green; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="back"><a href="menu.php">← Wróć</a></div>
    <div class="container">
        <h2>Dodaj nową książkę</h2>
        <?php if (!empty($msg)): ?>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>
        <form method="post">
            <label>Tytuł:</label>
            <input type="text" name="tytul" required>

            <label>Autor:</label>
            <input type="text" name="autor" required>

            <label>Wydawnictwo:</label>
            <input type="text" name="wydawnictwo" required>

            <label>Rok wydania:</label>
            <input type="number" name="rok_wydania" required min="1000" max="2099">

            <label>Kategoria:</label>
            <input type="text" name="kategoria" required>

            <button type="submit">Dodaj książkę</button>
        </form>
    </div>
</body>
</html>

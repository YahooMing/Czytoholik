<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'pracownik') {
    header("Location: index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zapisz'])) {
    $id = $_POST['id_ksiazki'];
    $tytul = $_POST['tytul'];
    $autor = $_POST['autor'];
    $wydawnictwo = $_POST['wydawnictwo'];
    $rok = $_POST['rok_wydania'];
    $kategoria = $_POST['kategoria'];

    $stmt = $conn->prepare("UPDATE ksiazki SET tytul=?, autor=?, wydawnictwo=?, rok_wydania=?, kategoria=? WHERE id_ksiazki=?");
    $stmt->bind_param("sssisi", $tytul, $autor, $wydawnictwo, $rok, $kategoria, $id);
    $stmt->execute();
    $stmt->close();

    $komunikat = "Książka została zaktualizowana.";
}

$wybrana_ksiazka = null;
if (isset($_GET['id_ksiazki'])) {
    $stmt = $conn->prepare("SELECT * FROM ksiazki WHERE id_ksiazki = ?");
    $stmt->bind_param("i", $_GET['id_ksiazki']);
    $stmt->execute();
    $result = $stmt->get_result();
    $wybrana_ksiazka = $result->fetch_assoc();
    $stmt->close();
}

$ksiazki = $conn->query("SELECT * FROM ksiazki");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj książkę</title>
    <style>
        body { font-family: Arial; background-color: #f9f9f9; padding: 20px; }
        .container { background: white; padding: 20px; max-width: 600px; margin: auto; border-radius: 10px; }
        h2 { text-align: center; }
        form { display: flex; flex-direction: column; gap: 10px; }
        label { font-weight: bold; }
        input, select, button { padding: 8px; font-size: 16px; }
        .back { position: absolute; top: 20px; right: 20px; }
        .info { color: green; text-align: center; }
    </style>
</head>
<body>
    <a class="back" href="menu.php">🔙 Wróć</a>
    <div class="container">
        <h2>Edytuj książkę</h2>

        <?php if (isset($komunikat)): ?>
            <p class="info"><?= $komunikat ?></p>
        <?php endif; ?>

        <form method="get">
            <label for="id_ksiazki">Wybierz książkę:</label>
            <select name="id_ksiazki" onchange="this.form.submit()">
                <option value="">-- wybierz --</option>
                <?php while ($k = $ksiazki->fetch_assoc()): ?>
                    <option value="<?= $k['id_ksiazki'] ?>" <?= (isset($_GET['id_ksiazki']) && $_GET['id_ksiazki'] == $k['id_ksiazki']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['tytul']) ?> (<?= $k['autor'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <?php if ($wybrana_ksiazka): ?>
            <form method="post">
                <input type="hidden" name="id_ksiazki" value="<?= $wybrana_ksiazka['id_ksiazki'] ?>">

                <label>Tytuł:</label>
                <input type="text" name="tytul" value="<?= htmlspecialchars($wybrana_ksiazka['tytul']) ?>" required>

                <label>Autor:</label>
                <input type="text" name="autor" value="<?= htmlspecialchars($wybrana_ksiazka['autor']) ?>" required>

                <label>Wydawnictwo:</label>
                <input type="text" name="wydawnictwo" value="<?= htmlspecialchars($wybrana_ksiazka['wydawnictwo']) ?>" required>

                <label>Rok wydania:</label>
                <input type="number" name="rok_wydania" value="<?= $wybrana_ksiazka['rok_wydania'] ?>" required>

                <label>Kategoria:</label>
                <input type="text" name="kategoria" value="<?= htmlspecialchars($wybrana_ksiazka['kategoria']) ?>" required>

                <button type="submit" name="zapisz">Zapisz zmiany</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

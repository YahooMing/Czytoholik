<?php
session_start();
require_once "conn.php";
if (!isset($_SESSION['id_czytelnika'])) {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['id_czytelnika'];
$komunikat = "";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $stmt = $conn->prepare("SELECT imie, nazwisko, adres, telefon, email FROM czytelnicy WHERE id_czytelnika = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imie, $nazwisko, $adres, $telefon, $email);
    $stmt->fetch();
    $stmt->close();
} else {
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $adres = $_POST['adres'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE czytelnicy SET imie=?, nazwisko=?, adres=?, telefon=?, email=? WHERE id_czytelnika=?");
    $stmt->bind_param("sssssi", $imie, $nazwisko, $adres, $telefon, $email, $id);
    if ($stmt->execute()) {
        $komunikat = "Dane zostały zaktualizowane.";
    } else {
        $komunikat = "Błąd podczas aktualizacji.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj dane</title>
    <style>
        body { font-family: Arial; background-color: #f0f0f0; padding: 20px; }
        .container { background: white; max-width: 600px; margin: auto; padding: 20px; border-radius: 10px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="email"] { width: 100%; padding: 8px; margin-top: 5px; }
    </style>
</head>
<body>
<a class="back" href="menu.php"><-- Wróć</a>
<div class="container">
    <h2>Edytuj swoje dane</h2>
    <?php if ($komunikat): ?>
        <p class="message"><?= $komunikat ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Imię:
            <input type="text" name="imie" value="<?= htmlspecialchars($imie ?? '') ?>" required>
        </label>
        <label>Nazwisko:
            <input type="text" name="nazwisko" value="<?= htmlspecialchars($nazwisko ?? '') ?>" required>
        </label>
        <label>Adres:
            <input type="text" name="adres" value="<?= htmlspecialchars($adres ?? '') ?>" required>
        </label>
        <label>Telefon:
            <input type="text" name="telefon" value="<?= htmlspecialchars($telefon ?? '') ?>" required>
        </label>
        <label>Email:
            <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
        </label>
        <button type="submit">Zapisz zmiany</button>
    </form>
</div>
</body>
</html>

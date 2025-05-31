<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'pracownik') {
    header("Location: index.php");
    exit();
}

$komunikat = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $adres = $_POST['adres'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];

    $kiki = $conn->prepare("INSERT INTO czytelnicy (imie, nazwisko, adres, telefon, email) VALUES (?, ?, ?, ?, ?)");
    $kiki-> bind_param("sssss", $imie, $nazwisko, $adres, $telefon, $email);

    if ($kiki->execute()) {
        $komunikat = "czytelnk został dodany pomyślnie.";
    } else {
        $komunikat = "ERROR podczas dodawania czytelnika";
    }
    $kiki->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <link rel="stylesheet" type="text/css" href="button.css">
    <meta charset="UTF-8">
    <title>dodaj_czytelnika</title>
    <style>
        body { font-family: Arial; background-color: #f2f2f2; padding: 20px; }
        .container { background: white; padding: 30px; max-width: 500px; margin: 60px auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        form { display: flex; flex-direction: column; gap: 15px; }
        input[type="text"], input[type="email"] { padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #388e3c; }
        .message { text-align: center; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="back-button">
        <a href="menu.php"><--- Wróć</a>
    </div>
    <div class="container">
        <h2>Dodaj czytelnika</h2>
        <form method="post">
            <input type="text" name="imie" placeholder="Imię" required>
            <input type="text" name="nazwisko" placeholder="Nazwisko" required>
            <input type="text" name="adres" placeholder="Adres" required>
            <input type="text" name="telefon" placeholder="Telefon" required>
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit">Dodaj</button>
        </form>
        <?php if (!empty($komunikat)): ?>
            <div class="message"><?= $komunikat ?></div>
        <?php endif; ?>
    </div>
</body>
</html>

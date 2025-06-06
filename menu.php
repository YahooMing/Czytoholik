<?php
session_start();
require_once "conn.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rola'])){
    $_SESSION['rola'] = $_POST['rola'];

    if ($_SESSION['rola'] === 'czytelnik'){
        $_SESSION['id_czytelnika'] = 1;
    }
}

if (!isset($_SESSION['rola'])){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>menu</title>
    <style>
        body { font-family: Arial; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 50px auto; padding: 30px; background: white; border-radius: 10px; }
        h2 { text-align: center; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
    </style>
</head>
<body>
<div class="back-button">
    <a href="index.php"><-- Zmień użytkownika</a>
</div>
    <div class="container">
        <h2>Siema, <?= $_SESSION['rola'] === 'pracownik' ? 'pracowniku' : 'czytelniku' ?>!</h2>
        <ul>
        <?php if ($_SESSION['rola'] === 'pracownik'): ?>
            <li><a href="dodaj_ksiazke.php">Dodaj książkę (Create)</a></li>
            <li><a href="lista_ksiazek.php">Przeglądaj książki (Read)</a></li>
            <li><a href="edytuj_ksiazke.php">Edytuj książkę (Update)</a></li>
            <li><a href="usun_ksiazke.php">Usuń książkę (Delete)</a></li>
            <li><a href="dodaj_egzemplarz.php">Dodaj egzemplarz (Create)</a></li>
            <li><a href="dodaj_czytelnika.php">Dodaj czytelnika (Create)</a></li>
            <li><a href="lista_czytelnikow.php">Wyświetl czytelników (Read)</a></li>
        <?php else: ?>
            <li><a href="rezerwuj.php">Zarezerwuj książkę (Create)</a></li>
            <li><a href="moje_rezerwacje.php">Moje rezerwacje (Read)</a></li>
            <li><a href="anuluj_rezerwacje.php">Anuluj rezerwację (Delete)</a></li>
            <li><a href="moje_wypozyczenia.php">Moje wypożyczenia (Read)</a></li>
            <li><a href="edytuj_mnie.php">Moje dane (Update)</a></li>

            <?php
            // musze sprawdzic czy są jakies kary zanim dam mozliwosc zapłaty
            $kiki = $conn->prepare("SELECT COUNT(*) FROM kary JOIN wypozyczenia ON wypozyczenia.id_wypozyczenia = kary.w_id WHERE wypozyczenia.c_id = ? AND kary.kwota_kary > 0 AND kary.status_kary != 'zapłacona'");
            $kiki->bind_param("i", $_SESSION['id_czytelnika']);
            $kiki->execute();
            $kiki->bind_result($ilosc);
            $kiki->fetch();
            $kiki->close();

            if ($ilosc > 0): ?>
                <li><a href="splac_kare.php">Spłać karę (Update)</a></li>
            <?php endif; ?>
        <?php endif; ?>
        </ul>
    </div>
</body>
</html>

<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'pracownik') {
    header("Location: index.php");
    exit();
}

$sql = "
    SELECT
        k.id_ksiazki,
        k.tytul,
        k.autor,
        k.wydawnictwo,
        k.rok_wydania,
        k.kategoria,
        COUNT(e.id_egzemplarz) AS liczba_egzemplarzy
    FROM ksiazki k
    LEFT JOIN egzemplarze e ON k.id_ksiazki = e.k_id
    GROUP BY k.id_ksiazki
    ORDER BY k.tytul ASC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Przeglądaj książki</title>
    <style>
        body { font-family: Arial; background-color: #f9f9f9; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="back"><a href="menu.php">← Wróć</a></div>
    <div class="container">
        <h2>Lista książek</h2>
        <table>
            <tr>
                <th>Tytuł</th>
                <th>Autor</th>
                <th>Wydawnictwo</th>
                <th>Rok</th>
                <th>Kategoria</th>
                <th>Egzemplarze</th>
                <th>Akcje</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['tytul']) ?></td>
                <td><?= htmlspecialchars($row['autor']) ?></td>
                <td><?= htmlspecialchars($row['wydawnictwo']) ?></td>
                <td><?= htmlspecialchars($row['rok_wydania']) ?></td>
                <td><?= htmlspecialchars($row['kategoria']) ?></td>
                <td><?= $row['liczba_egzemplarzy'] ?></td>
                <td>
                    <a href="edytuj_ksiazke.php?id=<?= $row['id_ksiazki'] ?>">Edytuj</a> |
                    <a href="usun_ksiazke.php?id=<?= $row['id_ksiazki'] ?>" onclick="return confirm('Na pewno chcesz usunąć książkę?')">Usuń</a> |
                    <a href="dodaj_egzemplarz.php?id=<?= $row['id_ksiazki'] ?>">Dodaj egzemplarz</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'czytelnik') {
    header("Location: index.php");
    exit();
}

$id_czytelnika = $_SESSION['id_czytelnika'];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje wypożyczenia</title>
    <style>
        body { font-family: Arial; background-color: #f9f9f9; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #eee; }
        .back { position: absolute; top: 20px; right: 20px; }
        .back a { text-decoration: none; background: #4285F4; color: white; padding: 8px 12px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="back"><a href="menu.php">← Wróć</a></div>
    <div class="container">
        <h2>Moje wypożyczenia</h2>

        <?php
        $sql = "
            SELECT w.id_wypozyczenia, k.tytul, k.autor, e.egzemplarz,
                   w.data_wypozyczenia, w.przewidywana_data_zwrotu, w.data_zwrotu
            FROM wypozyczenia w
            JOIN egzemplarze e ON w.e_id = e.id_egzemplarz
            JOIN ksiazki k ON e.k_id = k.id_ksiazki
            WHERE w.c_id = ?
            ORDER BY w.data_wypozyczenia DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_czytelnika);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Tytuł</th>
                    <th>Autor</th>
                    <th>Egzemplarz</th>
                    <th>Data wypożyczenia</th>
                    <th>Przewidywana data zwrotu</th>
                    <th>Data zwrotu</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['tytul']) ?></td>
                        <td><?= htmlspecialchars($row['autor']) ?></td>
                        <td><?= htmlspecialchars($row['egzemplarz']) ?></td>
                        <td><?= $row['data_wypozyczenia'] ?></td>
                        <td><?= $row['przewidywana_data_zwrotu'] ?></td>
                        <td><?= $row['data_zwrotu'] ?? '<i>jeszcze nie zwrócono</i>' ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Brak wypożyczeń.</p>
        <?php endif; ?>
    </div>
</body>
</html>

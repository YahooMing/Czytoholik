<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'czytelnik') {
    header("Location: index.php");
    exit();
}

$id_czytelnika = $_SESSION['id_czytelnika'];

// Obsługa spłaty kary
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_kary'])) {
    $id_kary = intval($_POST['id_kary']);

    // Aktualizujemy status kary na "zapłacona"
    $stmt = $conn->prepare("UPDATE kary k
        JOIN wypozyczenia w ON k.w_id = w.id_wypozyczenia
        SET k.status_kary = 'zapłacona', k.kwota_kary = 0
        WHERE k.id_kary = ? AND w.c_id = ?");
    $stmt->bind_param("ii", $id_kary, $id_czytelnika);
    $stmt->execute();
    $stmt->close();

    $msg = "Kara została spłacona.";
}

// Pobieramy niezapłacone kary użytkownika
$sql = "SELECT k.id_kary, k.kwota_kary, w.data_wypozyczenia, k.status_kary,
               ks.tytul, e.egzemplarz
        FROM kary k
        JOIN wypozyczenia w ON k.w_id = w.id_wypozyczenia
        JOIN egzemplarze e ON w.e_id = e.id_egzemplarz
        JOIN ksiazki ks ON e.k_id = ks.id_ksiazki
        WHERE w.c_id = ? AND k.status_kary != 'zapłacona' AND k.kwota_kary > 0";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_czytelnika);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Spłać karę</title>
    <style>
        body { font-family: Arial; background-color: #fafafa; padding: 20px; }
        .container { max-width: 700px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #eee; }
        button { background-color: #28a745; color: white; border: none; padding: 8px 12px; cursor: pointer; border-radius: 5px; }
        button:hover { background-color: #218838; }
        .back { position: absolute; top: 20px; right: 20px; }
        .back a { text-decoration: none; background: #4285F4; color: white; padding: 8px 12px; border-radius: 5px; }
        .msg { margin: 10px 0; color: green; font-weight: bold; }
    </style>
</head>
<body>
    <div class="back"><a href="menu.php">← Wróć</a></div>
    <div class="container">
        <h2>Spłać karę</h2>

        <?php if (!empty($msg)): ?>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Tytuł książki</th>
                    <th>Egzemplarz</th>
                    <th>Data wypożyczenia</th>
                    <th>Kwota kary</th>
                    <th>Akcja</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['tytul']) ?></td>
                        <td><?= htmlspecialchars($row['egzemplarz']) ?></td>
                        <td><?= $row['data_wypozyczenia'] ?></td>
                        <td><?= $row['kwota_kary'] ?> zł</td>
                        <td>
                            <form method="post" onsubmit="return confirm('Czy na pewno chcesz spłacić tę karę?');">
                                <input type="hidden" name="id_kary" value="<?= $row['id_kary'] ?>">
                                <button type="submit">Spłać</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Nie masz niezapłaconych kar.</p>
        <?php endif; ?>
    </div>
</body>
</html>

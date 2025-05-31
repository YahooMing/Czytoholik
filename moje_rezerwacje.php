<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'czytelnik') {
    header("Location: index.php");
    exit();
}

$id_czytelnika = $_SESSION['id_czytelnika'];

$sql = "
SELECT
    r.id_rezerwacji,
    r.data_rezerwacji,
    e.egzemplarz,
    e.stan,
    k.tytul,
    k.autor
FROM rezerwacje r
JOIN egzemplarze e ON r.e_id = e.id_egzemplarz
JOIN ksiazki k ON e.k_id = k.id_ksiazki
WHERE r.c_id = ?
ORDER BY r.data_rezerwacji DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_czytelnika);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje rezerwacje</title>
    <style>
        body { font-family: Arial; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ccc; text-align: left; }
        th { background-color: #f0f0f0; }
        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .back-button a {
            text-decoration: none;
            color: #4285F4;
            font-weight: bold;
        }
        .back-button a:hover {
            color: #306acb;
        }
        .btn-cancel {
            background: #e74c3c;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-cancel:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="back-button">
        <a href="menu.php">🔙 Wróć</a>
    </div>

    <div class="container">
        <h2>Moje rezerwacje</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Tytuł</th>
                        <th>Autor</th>
                        <th>Egzemplarz</th>
                        <th>Stan</th>
                        <th>Data rezerwacji</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['tytul']) ?></td>
                            <td><?= htmlspecialchars($row['autor']) ?></td>
                            <td><?= htmlspecialchars($row['egzemplarz']) ?></td>
                            <td><?= htmlspecialchars($row['stan']) ?></td>
                            <td><?= $row['data_rezerwacji'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak aktywnych rezerwacji.</p>
        <?php endif; ?>
    </div>
</body>
</html>

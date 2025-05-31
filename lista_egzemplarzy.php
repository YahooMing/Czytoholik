<?php
session_start();
require_once "conn.php";
if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'pracownik') {
    header("Location: index.php");
    exit();
}

$egzemplarze = [];
$tytul = '';
if (isset($_GET['id_ksiazki'])) {
    $id_ksiazki = $_GET['id_ksiazki'];

    $stmt = $conn->prepare("SELECT tytul FROM ksiazki WHERE id_ksiazki = ?");
    $stmt->bind_param("i", $id_ksiazki);
    $stmt->execute();
    $stmt->bind_result($tytul);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT id_egzemplarz, stan, egzemplarz FROM egzemplarze WHERE k_id = ?");
    $stmt->bind_param("i", $id_ksiazki);
    $stmt->execute();
    $result = $stmt->get_result();
    $egzemplarze = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Egzemplarze książki</title>
    <style>
        body { font-family: Arial; background-color: #f9f9f9; padding: 20px; }
        .container { background: white; padding: 20px; max-width: 700px; margin: auto; border-radius: 10px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        a.button { padding: 5px 10px; background-color: red; color: white; text-decoration: none; border-radius: 5px; }
        .back { position: absolute; top: 20px; right: 20px; }
    </style>
</head>
<body>
<a class="back" href="menu.php">🔙 Wróć</a>
<div class="container">
    <h2>Egzemplarze: <?= htmlspecialchars($tytul) ?></h2>

    <?php if ($egzemplarze): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kod</th>
                    <th>Stan</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($egzemplarze as $e): ?>
                    <tr>
                        <td><?= $e['id_egzemplarz'] ?></td>
                        <td><?= htmlspecialchars($e['egzemplarz']) ?></td>
                        <td><?= htmlspecialchars($e['stan']) ?></td>
                        <td>
                            <a class="button" href="usun_egzemplarz.php?id=<?= $e['id_egzemplarz'] ?>&ksiazka=<?= $id_ksiazki ?>" onclick="return confirm('Na pewno usunąć ten egzemplarz?')">Usuń</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Brak egzemplarzy tej książki.</p>
    <?php endif; ?>
</div>
</body>
</html>

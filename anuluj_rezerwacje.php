<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'czytelnik') {
    header("Location: index.php");
    exit();
}

$id_czytelnika = $_SESSION['id_czytelnika'];
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_rezerwacji'])) {
    $id_rezerwacji = intval($_POST['id_rezerwacji']);
    $check = $conn->prepare("SELECT id_rezerwacji FROM rezerwacje WHERE id_rezerwacji = ? AND c_id = ?");
    $check->bind_param("ii", $id_rezerwacji, $id_czytelnika);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $delete = $conn->prepare("DELETE FROM rezerwacje WHERE id_rezerwacji = ?");
        $delete->bind_param("i", $id_rezerwacji);
        $delete->execute();
        $message = "Rezerwacja została anulowana.";
    } else {
        $message = "Nieprawidłowa rezerwacja.";
    }
    $check->close();
}
$stmt = $conn->prepare("
    SELECT r.id_rezerwacji, r.data_rezerwacji, k.tytul, k.autor
    FROM rezerwacje r
    JOIN egzemplarze e ON r.e_id = e.id_egzemplarz
    JOIN ksiazki k ON e.k_id = k.id_ksiazki
    WHERE r.c_id = ?
");
$stmt->bind_param("i", $id_czytelnika);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Anuluj rezerwację</title>
    <style>
        body { font-family: Arial; background-color: #f9f9f9; padding: 20px; }
        .container { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #ccc; padding: 10px; text-align: left; }
        form { display: inline; }
        button { background-color: #d9534f; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #c9302c; }
        .message {
            text-align: center;
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="back-button">
        <a href="menu.php"><-- Wróć</a>
    </div>

    <div class="container">
        <h2>Anuluj rezerwację</h2>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Tytuł</th>
                    <th>Autor</th>
                    <th>Data rezerwacji</th>
                    <th>Akcja</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['tytul']) ?></td>
                        <td><?= htmlspecialchars($row['autor']) ?></td>
                        <td><?= $row['data_rezerwacji'] ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Na pewno chcesz anulować tę rezerwację?');">
                                <input type="hidden" name="id_rezerwacji" value="<?= $row['id_rezerwacji'] ?>">
                                <button type="submit">Anuluj</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Nie masz aktywnych rezerwacji.</p>
        <?php endif; ?>
    </div>
</body>
</html>

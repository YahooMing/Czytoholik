<?php
session_start();
require_once "conn.php";

if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'czytelnik') {
    header("Location: index.php");
    exit();
}

$id_czytelnika = $_SESSION['id_czytelnika'];

// Obsługa formularza
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $e_id = $_POST['e_id'];
    $data_rezerwacji = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO rezerwacje (e_id, c_id, data_rezerwacji) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $e_id, $id_czytelnika, $data_rezerwacji);
    $stmt->execute();

    header("Location: moje_rezerwacje.php");
    exit();
}

// Pobierz dostępne egzemplarze: NIE wypożyczone i NIE zarezerwowane
$sql = "
SELECT e.id_egzemplarz, k.tytul, k.autor, e.egzemplarz, e.stan
FROM egzemplarze e
JOIN ksiazki k ON e.k_id = k.id_ksiazki
WHERE e.id_egzemplarz NOT IN (
    SELECT e_id FROM rezerwacje
)
AND e.id_egzemplarz NOT IN (
    SELECT e_id FROM wypozyczenia WHERE data_zwrotu IS NULL
)";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rezerwuj książkę</title>
    <style>
        body { font-family: Arial; background-color: #f7f7f7; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        h2 { text-align: center; }
        form { margin-top: 20px; }
        select, button { width: 100%; padding: 10px; margin-top: 10px; }
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
    </style>
</head>
<body>
    <div class="back-button">
        <a href="menu.php">🔙 Wróć</a>
    </div>

    <div class="container">
        <h2>Rezerwuj książkę</h2>

        <?php if ($result->num_rows > 0): ?>
            <form method="post">
                <label for="e_id">Wybierz egzemplarz książki:</label>
                <select name="e_id" required>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['id_egzemplarz'] ?>">
                            <?= htmlspecialchars($row['tytul']) ?> (<?= $row['egzemplarz'] ?>, stan: <?= $row['stan'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Zarezerwuj</button>
            </form>
        <?php else: ?>
            <p>Brak dostępnych egzemplarzy do rezerwacji.</p>
        <?php endif; ?>
    </div>
</body>
</html>

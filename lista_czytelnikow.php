<?php
require_once "conn.php";
session_start();
$result = $conn->query("SELECT * FROM czytelnicy");
?>
<div class="back-button">
    <a href="menu.php"><-- Wróć</a>
</div>
<h2>Lista czytelników</h2>
<table border="1">
    <tr>
        <th>ID</th><th>Imię</th><th>Nazwisko</th><th>Telefon</th><th>Email</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id_czytelnika'] ?></td>
        <td><?= $row['imie'] ?></td>
        <td><?= $row['nazwisko'] ?></td>
        <td><?= $row['telefon'] ?></td>
        <td><?= $row['email'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

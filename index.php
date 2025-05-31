<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Biblioteka- wybierz użytkownika</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 20px; }
        .container { background: white; padding: 30px; max-width: 400px; margin: 100px auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        form { display: flex; flex-direction: column; gap: 15px; }
        button { padding: 10px; background-color: #4285F4; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #306acb; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Witamy w kolonii</h2>
        <form method="post" action="menu.php">
            <button type="submit" name="rola" value="czytelnik">Zaloguj jako Czytelnik</button>
            <button type="submit" name="rola" value="pracownik">Zaloguj jako Pracownik</button>
        </form>
    </div>
</body>
</html>

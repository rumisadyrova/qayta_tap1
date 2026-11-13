<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QaytaTap</title>
    <link rel="stylesheet" href="/qaytatap1/style.css">
</head>
<body>

<header>
    <h1>QaytaTap</h1>

    <nav>
        <a href="/qaytatap1/index.php">Басты бет</a>

        <?php if (isset($_SESSION["user_id"])): ?>

            <span>
                Сәлем, <?= htmlspecialchars($_SESSION["user_name"], ENT_QUOTES, "UTF-8") ?>!
            </span>

            <a href="/qaytatap1/create.php">Хабарландыру қосу</a>
            <a href="/qaytatap1/logout.php">Шығу</a>

        <?php else: ?>

            <a href="/qaytatap1/register.php">Тіркелу</a>
            <a href="/qaytatap1/login.php">Кіру</a>

        <?php endif; ?>
    </nav>
</header>

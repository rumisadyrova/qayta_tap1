<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/db.php";

$id = (int)($_GET["id"] ?? 0);

if ($id < 1) {
    die("Хабарландыру табылмады.");
}

// Тек өз хабарландыруын тексеру
$stmt = $pdo->prepare(
    "SELECT id FROM reports
     WHERE id = ? AND user_id = ?"
);

$stmt->execute([$id, $_SESSION["user_id"]]);

$report = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$report) {
    die("Бұл хабарландыруды жоюға рұқсатыңыз жоқ.");
}

// Хабарландыруды жою
$stmt = $pdo->prepare(
    "DELETE FROM reports
     WHERE id = ? AND user_id = ?"
);

$stmt->execute([$id, $_SESSION["user_id"]]);

header("Location: index.php");
exit;
?>
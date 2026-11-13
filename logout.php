<?php
session_start();

// Сессиядағы барлық деректерді жою
$_SESSION = [];

// Сессияны аяқтау
session_destroy();

// Басты бетке қайтару
header("Location: index.php");
exit;
?>
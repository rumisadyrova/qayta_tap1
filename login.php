<?php
session_start();
require_once __DIR__ . "/config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
    $error = "Барлық өрісті толтырыңыз!";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Email мекенжайын дұрыс енгізіңіз!";
} else {
        $stmt = $pdo->prepare(
            "SELECT id, name, email, password
             FROM users
             WHERE email = ?"
        );

        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];

            header("Location: index.php");
            exit;
        } else {
            $error = "Email немесе құпия сөз қате!";
        }
    }
}
?>

<?php include __DIR__ . "/includes/header.php"; ?>

<main class="content">
    <h1>QaytaTap — Аккаунтқа кіру</h1>
    <p>Қайта оралғаныңа қуаныштымыз!</p>

    <?php if ($error !== ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <p>
            <label>Email:</label><br>
            <input type="email" name="email" required>
        </p>

        <p>
            <label>Құпия сөз:</label><br>
            <input type="password" name="password" required>
        </p>

        <button type="submit">Кіру</button>
    </form>

    <p>
        Аккаунтың жоқ па?
        <a href="register.php">Тіркелу</a>
    </p>

    <p>
        <a href="index.php">← Басты бетке оралу</a>
    </p>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>

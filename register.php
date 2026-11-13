<?php
require_once __DIR__ . "/config/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $error = "Барлық өрісті толтырыңыз!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email мекенжайын дұрыс енгізіңіз!";
    } elseif (strlen($password) < 8) {
        $error = "Құпия сөз кемінде 8 таңбадан тұруы керек!";
    } else {
        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = "Бұл email бұрын тіркелген!";
        } else {
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $name,
                $email,
                $hashedPassword
            ]);

            $success = "Тіркелу сәтті аяқталды!";
        }
    }
}
?>

<?php include __DIR__ . "/includes/header.php"; ?>

<main class="content">
    <h1>QaytaTap — Тіркелу</h1>
    <p>Жоғалған заттарды іздеуді бізбен баста!</p>

    <?php if ($error !== ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <?php if ($success !== ""): ?>
        <p style="color: green;">
            <?= htmlspecialchars($success, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <p>
            <label>Аты-жөніңіз:</label><br>
            <input type="text" name="name"
                   required maxlength="100">
        </p>

        <p>
            <label>Email:</label><br>
            <input type="email" name="email"
                   required maxlength="150">
        </p>

        <p>
            <label>Құпия сөз:</label><br>
            <input type="password" name="password"
                   required minlength="8">
        </p>

        <button type="submit">Тіркелу</button>
    </form>

    <p>
        Аккаунтың бар ма?
        <a href="login.php">Кіру</a>
    </p>

    <p>
        <a href="index.php">← Басты бетке оралу</a>
    </p>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>

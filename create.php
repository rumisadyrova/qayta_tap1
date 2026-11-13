
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $type = $_POST["type"] ?? "";
    $location = trim($_POST["location"] ?? "");
    $event_date = $_POST["event_date"] ?? "";
    $category_id = (int)($_POST["category_id"] ?? 0);

    if (
        $title === "" ||
        $location === "" ||
        !in_array($type, ["lost", "found"], true) ||
        $category_id < 1
    ) {
        $message = "Барлық міндетті өрістерді дұрыс толтырыңыз.";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO reports
            (user_id, category_id, title, description,
             type, location, event_date)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $_SESSION["user_id"],
            $category_id,
            $title,
            $description,
            $type,
            $location,
            $event_date !== "" ? $event_date : null
        ]);

        $message = "Хабарландыру дерекқорға сақталды!";
    }
}

$categories = $pdo->query(
    "SELECT id, name FROM categories"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . "/includes/header.php"; ?>

<main class="content">
    <h1>Хабарландыру қосу</h1>

    <p>Жоғалған немесе табылған зат туралы ақпарат қалдырыңыз.</p>

    <?php if ($message !== ""): ?>
        <p>
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="create.php">

        <p>
            <label>Заттың атауы:</label>
            <input type="text" name="title"
                   required maxlength="200">
        </p>

        <p>
            <label>Түрі:</label>
            <select name="type" required>
                <option value="lost">Жоғалды</option>
                <option value="found">Табылды</option>
            </select>
        </p>

        <p>
            <label>Санаты:</label>
            <select name="category_id" required>
                <option value="">Санатты таңдаңыз</option>

                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category["id"] ?>">
                        <?= htmlspecialchars(
                            $category["name"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </p>

        <p>
            <label>Сипаттамасы:</label>
            <textarea name="description" rows="4"></textarea>
        </p>

        <p>
            <label>Қай жерде?</label>
            <input type="text" name="location"
                   required maxlength="255">
        </p>

        <p>
            <label>Күні:</label>
            <input type="date" name="event_date">
        </p>

        <button type="submit">Хабарландыруды сақтау</button>

    </form>

    <p>
        <a href="index.php">← Басты бетке оралу</a>
    </p>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
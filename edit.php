<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/db.php";

$message = "";

// Өңделетін хабарландырудың ID нөмірін алу
$id = (int)($_GET["id"] ?? 0);

if ($id < 1) {
    die("Хабарландыру табылмады.");
}

// Тек өз хабарландыруын алу
$stmt = $pdo->prepare(
    "SELECT * FROM reports
     WHERE id = ? AND user_id = ?"
);

$stmt->execute([$id, $_SESSION["user_id"]]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$report) {
    die("Бұл хабарландыруды өңдеуге рұқсатыңыз жоқ.");
}

// Өзгерістерді сақтау
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
            "UPDATE reports
             SET category_id = ?,
                 title = ?,
                 description = ?,
                 type = ?,
                 location = ?,
                 event_date = ?
             WHERE id = ? AND user_id = ?"
        );

        $stmt->execute([
            $category_id,
            $title,
            $description,
            $type,
            $location,
            $event_date !== "" ? $event_date : null,
            $id,
            $_SESSION["user_id"]
        ]);

        $message = "Хабарландыру сәтті жаңартылды!";

        // Жаңартылған деректерді қайта алу
        $stmt = $pdo->prepare(
            "SELECT * FROM reports
             WHERE id = ? AND user_id = ?"
        );

        $stmt->execute([$id, $_SESSION["user_id"]]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Санаттарды алу
$categories = $pdo->query(
    "SELECT id, name FROM categories"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . "/includes/header.php"; ?>

<main class="content">

    <h1>Хабарландыруды өңдеу</h1>

    <?php if ($message !== ""): ?>
        <p>
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $id ?>">

        <p>
            <label>Заттың атауы:</label>
            <input
                type="text"
                name="title"
                value="<?= htmlspecialchars($report["title"], ENT_QUOTES, "UTF-8") ?>"
                required
                maxlength="200"
            >
        </p>

        <p>
            <label>Түрі:</label>
            <select name="type" required>
                <option value="lost" <?= $report["type"] === "lost" ? "selected" : "" ?>>
                    Жоғалды
                </option>

                <option value="found" <?= $report["type"] === "found" ? "selected" : "" ?>>
                    Табылды
                </option>
            </select>
        </p>

        <p>
            <label>Санаты:</label>
            <select name="category_id" required>

                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= (int)$category["id"] ?>"
                        <?= (int)$report["category_id"] === (int)$category["id"] ? "selected" : "" ?>
                    >
                        <?= htmlspecialchars($category["name"], ENT_QUOTES, "UTF-8") ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </p>

        <p>
            <label>Сипаттамасы:</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($report["description"] ?? "", ENT_QUOTES, "UTF-8") ?></textarea>
        </p>

        <p>
            <label>Қай жерде?</label>
            <input
                type="text"
                name="location"
                value="<?= htmlspecialchars($report["location"], ENT_QUOTES, "UTF-8") ?>"
                required
                maxlength="255"
            >
        </p>

        <p>
            <label>Күні:</label>
            <input
                type="date"
                name="event_date"
                value="<?= htmlspecialchars($report["event_date"] ?? "", ENT_QUOTES, "UTF-8") ?>"
            >
        </p>

        <button type="submit">Өзгерістерді сақтау</button>

    </form>

    <p>
        <a href="index.php">← Басты бетке оралу</a>
    </p>

</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
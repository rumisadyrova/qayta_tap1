
<?php
require_once __DIR__ . "/config/db.php";

$search = trim($_GET["search"] ?? "");
$type = $_GET["type"] ?? "";

$sql = "SELECT reports.*, categories.name AS category_name
        FROM reports
        JOIN categories ON reports.category_id = categories.id
        WHERE (
            reports.title LIKE :search1
            OR reports.description LIKE :search2
            OR reports.location LIKE :search3
        )";

if (in_array($type, ["lost", "found"], true)) {
    $sql .= " AND reports.type = :type";
}

$sql .= " ORDER BY reports.created_at DESC";

$stmt = $pdo->prepare($sql);

$searchValue = "%" . $search . "%";

$stmt->bindValue(":search1", $searchValue);
$stmt->bindValue(":search2", $searchValue);
$stmt->bindValue(":search3", $searchValue);

if (in_array($type, ["lost", "found"], true)) {
    $stmt->bindValue(":type", $type);
}

$stmt->execute();

$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Жоғалған және табылған заттарды бөлу
$lostReports = array_filter($reports, function ($report) {
    return $report["type"] === "lost";
});

$foundReports = array_filter($reports, function ($report) {
    return $report["type"] === "found";
});
?>

<?php include __DIR__ . "/includes/header.php"; ?>

<!-- БАСТЫ БЕТТІҢ БАС БӨЛІГІ -->
<section class="hero">
    <p class="eyebrow">Тапқаныңды қайтар, жоғалтқаныңды тап!</p>

    <h1>Жоғалттың ба? <span>Бірге табайық!</span></h1>

    <p class="hero-text">
        Жоғалған немесе табылған зат туралы хабарландыруды
        қарап, қажетті ақпаратты оңай ізде.
    </p>

    <form class="search-form" method="GET" action="index.php">
        <input
            type="search"
            name="search"
            placeholder="Заттың атауын немесе орнын жаз..."
            value="<?= htmlspecialchars($search, ENT_QUOTES, "UTF-8") ?>"
        >

        <button type="submit">Іздеу</button>
    </form>

    <div class="filters">
        <a href="index.php">Барлығы</a>
        <a href="index.php?type=lost">Жоғалған заттар</a>
        <a href="index.php?type=found">Табылған заттар</a>
    </div>
</section>


<!-- ХАБАРЛАНДЫРУЛАР -->
<main class="reports-page" id="reports">

    <div class="reports-heading">
        <h1>Заттар туралы хабарландырулар</h1>

        <p>
            Жоғалған немесе табылған заттар туралы
            ақпаратты осы жерден таба аласыз.
        </p>

        <a class="add-button" href="create.php">
            + Хабарландыру қосу
        </a>
    </div>


    <!-- ЖОҒАЛҒАН ЗАТТАР -->
    <section class="report-group lost-group">

        <div class="group-heading">
            <div>
                <h2>Жоғалған заттар</h2>
                <p>Иесін күтіп жүрген заттар</p>
            </div>

            <span class="group-count">
                <?= count($lostReports) ?> хабарландыру
            </span>
        </div>

        <div class="cards">

            <?php if (empty($lostReports)): ?>

                <div class="empty-state">
                    <h3>Әзірге жоғалған заттар жоқ</h3>
                    <p>Бұл бөлімде хабарландырулар пайда болады.</p>
                </div>

            <?php else: ?>

                <?php foreach ($lostReports as $report): ?>

                    <article class="report-card">

                        <div class="card-top">
                            <span class="tag lost">Жоғалды</span>

                            <span class="category">
                                <?= htmlspecialchars(
                                    $report["category_name"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </span>
                        </div>

                        <h3>
                            <?= htmlspecialchars(
                                $report["title"],
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </h3>

                        <p class="description">
                            <?= htmlspecialchars(
                                $report["description"] ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </p>

                        <div class="card-details">
                            <p>
                                <strong>Орны:</strong>
                                <?= htmlspecialchars(
                                    $report["location"] ?? "Көрсетілмеген",
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </p>

                            <?php if (!empty($report["event_date"])): ?>
                                <p>
                                    <strong>Күні:</strong>
                                    <?= htmlspecialchars(
                                        $report["event_date"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </section>


    <!-- ТАБЫЛҒАН ЗАТТАР -->
    <section class="report-group found-group">

        <div class="group-heading">
            <div>
                <h2>Табылған заттар</h2>
                <p>Иелерін іздеп жүрген заттар</p>
            </div>

            <span class="group-count">
                <?= count($foundReports) ?> хабарландыру
            </span>
        </div>

        <div class="cards">

            <?php if (empty($foundReports)): ?>

                <div class="empty-state">
                    <h3>Әзірге табылған заттар жоқ</h3>
                    <p>Бұл бөлімде хабарландырулар пайда болады.</p>
                </div>

            <?php else: ?>

                <?php foreach ($foundReports as $report): ?>

                    <article class="report-card">

                        <div class="card-top">
                            <span class="tag found">Табылды</span>

                            <span class="category">
                                <?= htmlspecialchars(
                                    $report["category_name"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </span>
                        </div>

                        <h3>
                            <?= htmlspecialchars(
                                $report["title"],
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </h3>

                        <p class="description">
                            <?= htmlspecialchars(
                                $report["description"] ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </p>

                        <div class="card-details">
                            <p>
                                <strong>Орны:</strong>
                                <?= htmlspecialchars(
                                    $report["location"] ?? "Көрсетілмеген",
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </p>

                            <?php if (!empty($report["event_date"])): ?>
                                <p>
                                    <strong>Күні:</strong>
                                    <?= htmlspecialchars(
                                        $report["event_date"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php include __DIR__ . "/includes/footer.php"; ?>

<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('Invalid ID');
}

$db = Database::getInstance();
$vision = $db->fetchOne("SELECT * FROM vision_sdgs WHERE id = ?", [$id]);
if (!$vision) {
    die('Vision entry not found');
}
?>

<section class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($vision['title']); ?></h1>
        <p><?php echo htmlspecialchars($vision['description']); ?></p>
    </div>
</section>

<section class="container">
    <p><?php echo htmlspecialchars($vision['details']); ?></p>
</section>

<?php require_once 'includes/footer.php'; ?>
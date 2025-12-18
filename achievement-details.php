<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('Invalid ID');
}

$db = Database::getInstance();
$achievement = $db->fetchOne("SELECT * FROM achievements WHERE id = ?", [$id]);
if (!$achievement) {
    die('Achievement not found');
}
?>

<section class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($achievement['title']); ?></h1>
        <p><?php echo htmlspecialchars($achievement['description']); ?></p>
    </div>
</section>

<section class="container">
    <?php if ($achievement['image']): ?>
    <img src="<?php echo UPLOAD_URL . $achievement['image']; ?>" alt="<?php echo htmlspecialchars($achievement['title']); ?>">
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
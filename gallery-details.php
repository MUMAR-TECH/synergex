<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('Invalid ID');
}

$db = Database::getInstance();
$image = $db->fetchOne("SELECT * FROM gallery WHERE id = ?", [$id]);
if (!$image) {
    die('Image not found');
}
?>

<section class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($image['title']); ?></h1>
        <p><?php echo htmlspecialchars($image['caption']); ?></p>
    </div>
</section>

<section class="container">
    <img src="<?php echo UPLOAD_URL . $image['image']; ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
</section>

<?php require_once 'includes/footer.php'; ?>
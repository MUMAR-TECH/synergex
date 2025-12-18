<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('Invalid ID');
}

$db = Database::getInstance();
$service = $db->fetchOne("SELECT * FROM what_we_do WHERE id = ?", [$id]);
if (!$service) {
    die('Service not found');
}
?>

<section class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($service['title']); ?></h1>
        <p><?php echo htmlspecialchars($service['description']); ?></p>
    </div>
</section>

<section class="container">
    <ul>
        <?php foreach (explode('|', $service['features']) as $feature): ?>
        <li><?php echo htmlspecialchars($feature); ?></li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require_once 'includes/footer.php'; ?>
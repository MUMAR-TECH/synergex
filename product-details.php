<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('Invalid ID');
}

$db = Database::getInstance();
$product = $db->fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
if (!$product) {
    die('Product not found');
}
?>

<section class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p><?php echo htmlspecialchars($product['description']); ?></p>
    </div>
</section>

<section class="container">
    <img src="<?php echo UPLOAD_URL . $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    <p>Price: K<?php echo number_format($product['price'], 2); ?></p>
    <ul>
        <?php foreach (explode('|', $product['features']) as $feature): ?>
        <li><?php echo htmlspecialchars($feature); ?></li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require_once 'includes/footer.php'; ?>
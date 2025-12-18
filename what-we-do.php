<?php
// ============================================================================
// FILE: what-we-do.php - What We Do Detailed Page
// ============================================================================
require_once 'includes/header.php';
require_once 'includes/db.php';

$db = Database::getInstance();
$services = $db->fetchAll("SELECT * FROM what_we_do ORDER BY id ASC");
?>

<section class="page-header">
    <div class="container">
        <h1>What We Do</h1>
        <p>Comprehensive solutions for sustainable waste management</p>
    </div>
</section>

<section class="container">
    <div class="services-grid">
        <?php foreach ($services as $service): ?>
        <div class="service-card fade-in">
            <div style="font-size: 3rem; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($service['icon']); ?>
            </div>
            <h3><a href="what-we-do-details.php?id=<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['title']); ?></a></h3>
            <p><?php echo htmlspecialchars($service['description']); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

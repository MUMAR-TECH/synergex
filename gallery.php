<?php
// ============================================================================
// FILE: gallery.php - Gallery Page
// ============================================================================
require_once 'includes/header.php';

$categories = ['waste_collection', 'recycling', 'production', 'installation', 'community'];
$selectedCategory = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;
$galleryImages = getGallery($selectedCategory);
?>

<section class="page-header">
    <div class="container">
        <h1>Gallery</h1>
        <p>See our work in action - from waste collection to finished products</p>
    </div>
</section>

<section class="container">
    <!-- Category Filter -->
    <div class="gallery-filters" style="text-align: center; margin-bottom: 2rem;">
        <a href="gallery.php" class="btn <?php echo !$selectedCategory ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
        <a href="gallery.php?category=waste_collection" class="btn <?php echo $selectedCategory == 'waste_collection' ? 'btn-primary' : 'btn-secondary'; ?>">Waste Collection</a>
        <a href="gallery.php?category=recycling" class="btn <?php echo $selectedCategory == 'recycling' ? 'btn-primary' : 'btn-secondary'; ?>">Recycling</a>
        <a href="gallery.php?category=production" class="btn <?php echo $selectedCategory == 'production' ? 'btn-primary' : 'btn-secondary'; ?>">Production</a>
        <a href="gallery.php?category=installation" class="btn <?php echo $selectedCategory == 'installation' ? 'btn-primary' : 'btn-secondary'; ?>">Installation</a>
        <a href="gallery.php?category=community" class="btn <?php echo $selectedCategory == 'community' ? 'btn-primary' : 'btn-secondary'; ?>">Community</a>
    </div>
    
    <?php if (empty($galleryImages)): ?>
    <div style="text-align: center; padding: 4rem 2rem;">
        <p style="font-size: 1.2rem; color: #666;">No images available in this category yet.</p>
        <p>Check back soon for updates!</p>
    </div>
    <?php else: ?>
    <div class="gallery-grid">
        <?php foreach ($galleryImages as $image): ?>
        <div class="gallery-item fade-in">
            <img src="<?php echo UPLOAD_URL . $image['image']; ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
            <div class="gallery-caption">
                <h4><?php echo htmlspecialchars($image['title']); ?></h4>
                <?php if ($image['caption']): ?>
                <p><?php echo htmlspecialchars($image['caption']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>

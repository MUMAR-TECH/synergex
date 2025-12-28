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

// Parse gallery images if available
$galleryImages = [];
if (!empty($achievement['gallery_images'])) {
    $galleryImages = array_filter(array_map('trim', explode(',', $achievement['gallery_images'])));
}
?>

<section class="page-header" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-orange) 100%); color: white; padding: 4rem 2rem; text-align: center;">
    <div class="container">
        <div style="display: inline-block; background: rgba(255,255,255,0.2); padding: 0.5rem 1.5rem; border-radius: 50px; margin-bottom: 1rem;">
            <i class="fas fa-trophy"></i> <?php echo ucfirst($achievement['category']); ?> • <?php echo $achievement['year']; ?>
        </div>
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($achievement['title']); ?></h1>
        <p style="font-size: 1.2rem; max-width: 800px; margin: 0 auto; opacity: 0.95;"><?php echo htmlspecialchars($achievement['description']); ?></p>
    </div>
</section>

<section class="container" style="padding: 3rem 2rem;">
    <div style="max-width: 1000px; margin: 0 auto;">
        
        <!-- Main Image -->
        <?php if ($achievement['image']): ?>
        <div style="margin-bottom: 3rem;">
            <img src="<?php echo UPLOAD_URL . $achievement['image']; ?>" 
                 alt="<?php echo htmlspecialchars($achievement['title']); ?>"
                 style="width: 100%; height: auto; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
        </div>
        <?php endif; ?>
        
        <!-- Detailed Content -->
        <?php if (!empty($achievement['detailed_content'])): ?>
        <div style="background: white; padding: 2.5rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 3rem;">
            <h2 style="color: var(--primary-blue); margin-bottom: 1.5rem; font-size: 1.8rem;">
                <i class="fas fa-info-circle"></i> About This Achievement
            </h2>
            <div style="line-height: 1.8; font-size: 1.1rem; color: var(--text-dark); white-space: pre-line;">
                <?php echo nl2br(htmlspecialchars($achievement['detailed_content'])); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Gallery Images -->
        <?php if (!empty($galleryImages)): ?>
        <div style="margin-bottom: 3rem;">
            <h2 style="color: var(--primary-blue); margin-bottom: 1.5rem; font-size: 1.8rem;">
                <i class="fas fa-images"></i> Gallery
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                <?php foreach ($galleryImages as $index => $image): ?>
                <div style="position: relative; overflow: hidden; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;" 
                     onmouseover="this.style.transform='scale(1.05)'" 
                     onmouseout="this.style.transform='scale(1)'">
                    <img src="<?php echo htmlspecialchars($image); ?>" 
                         alt="Gallery image <?php echo $index + 1; ?>"
                         style="width: 100%; height: 250px; object-fit: cover; display: block; cursor: pointer;"
                         onclick="openImageModal('<?php echo htmlspecialchars($image); ?>')">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Achievement Info Card -->
        <div style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-orange) 100%); color: white; padding: 2rem; border-radius: 15px; margin-bottom: 3rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; text-align: center;">
                <div>
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;"><i class="fas fa-calendar-alt"></i></div>
                    <div style="font-size: 2rem; font-weight: 700;"><?php echo $achievement['year']; ?></div>
                    <div style="opacity: 0.9;">Year</div>
                </div>
                <div>
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <?php 
                        $icons = [
                            'grant' => 'fa-hand-holding-usd',
                            'pilot' => 'fa-rocket',
                            'recognition' => 'fa-award',
                            'partnership' => 'fa-handshake',
                            'media' => 'fa-newspaper'
                        ];
                        echo '<i class="fas ' . ($icons[$achievement['category']] ?? 'fa-trophy') . '"></i>';
                        ?>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 700;"><?php echo ucfirst($achievement['category']); ?></div>
                    <div style="opacity: 0.9;">Category</div>
                </div>
            </div>
        </div>
        
        <!-- Back Button -->
        <div style="text-align: center;">
            <a href="<?php echo SITE_URL; ?>/achievements.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i> Back to All Achievements
            </a>
        </div>
        
    </div>
</section>

<!-- Image Modal -->
<div id="imageModal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.9);" onclick="closeImageModal()">
    <span style="position: absolute; top: 30px; right: 50px; color: white; font-size: 40px; font-weight: bold; cursor: pointer;" onclick="closeImageModal()">&times;</span>
    <img id="modalImage" src="" style="margin: auto; display: block; max-width: 90%; max-height: 90%; margin-top: 50px;">
</div>

<script>
function openImageModal(imageSrc) {
    document.getElementById('imageModal').style.display = 'block';
    document.getElementById('modalImage').src = imageSrc;
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
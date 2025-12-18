<?php
// ============================================================================
// FILE: achievements.php - Achievements & Impact Page
// ============================================================================
require_once 'includes/header.php';
$achievements = getAchievements();
?>

<section class="page-header">
    <div class="container">
        <h1>Achievements & Impact</h1>
        <p>Milestones in our journey toward a sustainable future</p>
    </div>
</section>

<section class="container">
    <?php if (empty($achievements)): ?>
    <div style="text-align: center; padding: 4rem 2rem;">
        <p style="font-size: 1.2rem; color: #666;">No achievements recorded yet.</p>
    </div>
    <?php else: ?>
    <div class="timeline">
        <?php foreach ($achievements as $achievement): ?>
        <div class="timeline-item fade-in">
            <div class="timeline-year"><?php echo $achievement['year']; ?></div>
            <div class="timeline-content">
                <h3><a href="achievement-details.php?id=<?php echo $achievement['id']; ?>"><?php echo htmlspecialchars($achievement['title']); ?></a></h3>
                <p><?php echo htmlspecialchars($achievement['description']); ?></p>
                <?php if ($achievement['image']): ?>
                <img src="<?php echo UPLOAD_URL . $achievement['image']; ?>" alt="<?php echo htmlspecialchars($achievement['title']); ?>" class="timeline-image">
                <?php endif; ?>
                <?php if ($achievement['category']): ?>
                <span class="badge" style="margin-top: 1rem;"><?php echo ucfirst(str_replace('_', ' ', $achievement['category'])); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<section style="background: var(--light-grey); padding: 4rem 2rem; text-align: center;">
    <div class="container">
        <h2>Our Growing Impact</h2>
        <p style="font-size: 1.1rem; margin-top: 1rem;">Every achievement brings us closer to a cleaner, greener Zambia</p>
        <div style="margin-top: 2rem;">
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">Partner With Us</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

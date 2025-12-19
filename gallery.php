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
    <div class="gallery-grid" id="galleryGrid">
        <?php foreach ($galleryImages as $index => $image): ?>
        <div class="gallery-item" data-index="<?php echo $index; ?>" style="animation-delay: <?php echo $index * 0.1; ?>s">
            <div class="gallery-item-inner" onclick="openLightbox(<?php echo $index; ?>)">
                <?php 
                $isVideo = isset($image['media_type']) && $image['media_type'] === 'video';
                if ($isVideo): ?>
                    <video src="<?php echo UPLOAD_URL . $image['image']; ?>" muted></video>
                    <div class="video-overlay">
                        <i class="play-icon">▶</i>
                    </div>
                <?php else: ?>
                    <img src="<?php echo UPLOAD_URL . $image['image']; ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
                <?php endif; ?>
                <div class="gallery-item-overlay">
                    <h3><?php echo htmlspecialchars($image['title']); ?></h3>
                    <?php if (!empty($image['caption'])): ?>
                        <p><?php echo htmlspecialchars($image['caption']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Lightbox Modal -->
    <div id="lightboxModal" class="lightbox-modal">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        
        <div class="lightbox-content">
            <button class="lightbox-nav lightbox-prev" onclick="changeSlide(-1)">❮</button>
            
            <div class="lightbox-main">
                <div id="lightboxMedia"></div>
                <div class="lightbox-info">
                    <h2 id="lightboxTitle"></h2>
                    <p id="lightboxCaption"></p>
                    <span id="lightboxCounter"></span>
                </div>
            </div>
            
            <button class="lightbox-nav lightbox-next" onclick="changeSlide(1)">❯</button>
        </div>
        
        <div class="lightbox-thumbnails" id="lightboxThumbnails"></div>
    </div>
    <?php endif; ?>
</section>

<style>
/* Gallery Grid Styles */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.gallery-item {
    opacity: 0;
    animation: fadeInUp 0.6s ease-out forwards;
    cursor: pointer;
    position: relative;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.gallery-item-inner {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    aspect-ratio: 4/3;
}

.gallery-item-inner:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.gallery-item-inner img,
.gallery-item-inner video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.gallery-item-inner:hover img,
.gallery-item-inner:hover video {
    transform: scale(1.1);
}

.video-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 102, 0, 0.9);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.play-icon {
    color: white;
    font-size: 24px;
    margin-left: 4px;
}

.gallery-item-inner:hover .video-overlay {
    transform: translate(-50%, -50%) scale(1.2);
}

.gallery-item-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    color: white;
    padding: 1.5rem;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.gallery-item-inner:hover .gallery-item-overlay {
    transform: translateY(0);
}

.gallery-item-overlay h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.2rem;
}

.gallery-item-overlay p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Lightbox Styles */
.lightbox-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.lightbox-close {
    position: absolute;
    top: 20px;
    right: 40px;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    z-index: 10001;
    transition: color 0.3s ease;
}

.lightbox-close:hover {
    color: #FF6600;
}

.lightbox-content {
    display: flex;
    align-items: center;
    justify-content: center;
    height: calc(100vh - 150px);
    padding: 20px;
}

.lightbox-main {
    flex: 1;
    max-width: 1200px;
    text-align: center;
}

#lightboxMedia img,
#lightboxMedia video {
    max-width: 100%;
    max-height: 70vh;
    border-radius: 10px;
    box-shadow: 0 10px 50px rgba(0,0,0,0.5);
}

#lightboxMedia video {
    width: 100%;
}

.lightbox-info {
    color: white;
    margin-top: 1.5rem;
    text-align: center;
}

#lightboxTitle {
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
    color: #FF6600;
}

#lightboxCaption {
    font-size: 1rem;
    color: #ddd;
    margin-bottom: 0.5rem;
}

#lightboxCounter {
    font-size: 0.9rem;
    color: #999;
}

.lightbox-nav {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    font-size: 40px;
    padding: 20px;
    cursor: pointer;
    border-radius: 5px;
    transition: all 0.3s ease;
    margin: 0 10px;
}

.lightbox-nav:hover {
    background: rgba(255, 102, 0, 0.8);
    transform: scale(1.1);
}

.lightbox-thumbnails {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.8);
    padding: 15px;
    display: flex;
    justify-content: center;
    gap: 10px;
    overflow-x: auto;
    max-width: 100%;
}

.lightbox-thumbnails::-webkit-scrollbar {
    height: 8px;
}

.lightbox-thumbnails::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.lightbox-thumbnails::-webkit-scrollbar-thumb {
    background: #FF6600;
    border-radius: 4px;
}

.lightbox-thumb {
    width: 80px;
    height: 60px;
    cursor: pointer;
    border-radius: 5px;
    opacity: 0.6;
    transition: all 0.3s ease;
    object-fit: cover;
    border: 2px solid transparent;
}

.lightbox-thumb:hover {
    opacity: 1;
    transform: scale(1.1);
}

.lightbox-thumb.active {
    opacity: 1;
    border-color: #FF6600;
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .lightbox-nav {
        font-size: 30px;
        padding: 10px;
    }
    
    .lightbox-close {
        top: 10px;
        right: 20px;
        font-size: 30px;
    }
    
    .lightbox-thumb {
        width: 60px;
        height: 45px;
    }
}
</style>

<script>
let currentSlide = 0;
const galleryData = <?php echo json_encode(array_values($galleryImages)); ?>;

function openLightbox(index) {
    currentSlide = index;
    document.getElementById('lightboxModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    showSlide(currentSlide);
    generateThumbnails();
}

function closeLightbox() {
    document.getElementById('lightboxModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Pause any playing videos
    const videos = document.querySelectorAll('#lightboxMedia video');
    videos.forEach(video => video.pause());
}

function changeSlide(direction) {
    currentSlide += direction;
    if (currentSlide >= galleryData.length) currentSlide = 0;
    if (currentSlide < 0) currentSlide = galleryData.length - 1;
    showSlide(currentSlide);
}

function showSlide(index) {
    const item = galleryData[index];
    const mediaContainer = document.getElementById('lightboxMedia');
    const isVideo = item.media_type === 'video';
    
    // Create media element
    if (isVideo) {
        mediaContainer.innerHTML = `<video controls autoplay src="<?php echo UPLOAD_URL; ?>${item.image}"></video>`;
    } else {
        mediaContainer.innerHTML = `<img src="<?php echo UPLOAD_URL; ?>${item.image}" alt="${item.title}">`;
    }
    
    // Update info
    document.getElementById('lightboxTitle').textContent = item.title;
    document.getElementById('lightboxCaption').textContent = item.caption || '';
    document.getElementById('lightboxCounter').textContent = `${index + 1} / ${galleryData.length}`;
    
    // Update active thumbnail
    document.querySelectorAll('.lightbox-thumb').forEach((thumb, i) => {
        thumb.classList.toggle('active', i === index);
    });
    
    // Scroll active thumbnail into view
    const activeThumb = document.querySelector('.lightbox-thumb.active');
    if (activeThumb) {
        activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
}

function generateThumbnails() {
    const container = document.getElementById('lightboxThumbnails');
    container.innerHTML = '';
    
    galleryData.forEach((item, index) => {
        const isVideo = item.media_type === 'video';
        
        if (isVideo) {
            const video = document.createElement('video');
            video.src = '<?php echo UPLOAD_URL; ?>' + item.image;
            video.className = 'lightbox-thumb';
            video.muted = true;
            video.onclick = () => {
                currentSlide = index;
                showSlide(currentSlide);
            };
            container.appendChild(video);
        } else {
            const img = document.createElement('img');
            img.src = '<?php echo UPLOAD_URL; ?>' + item.image;
            img.alt = item.title;
            img.className = 'lightbox-thumb';
            img.onclick = () => {
                currentSlide = index;
                showSlide(currentSlide);
            };
            container.appendChild(img);
        }
    });
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('lightboxModal');
    if (modal.style.display === 'block') {
        if (e.key === 'ArrowLeft') changeSlide(-1);
        if (e.key === 'ArrowRight') changeSlide(1);
        if (e.key === 'Escape') closeLightbox();
    }
});

// Close lightbox when clicking outside the image
document.getElementById('lightboxModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLightbox();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Video.php';

$videoModel = new Video();
$videos = $videoModel->getActiveVideos();
?>

<style>
.video-gallery-section {
    padding: 30px 0;
}
.video-gallery-header {
    background: #1e3c72;
    color: white;
    padding: 12px 20px;
    font-size: 18px;
    font-weight: bold;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.video-scroll-btns {
    display: flex;
    gap: 5px;
}
.video-scroll-btn {
    width: 30px;
    height: 30px;
    background: #ffc107;
    color: #003366;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.video-scroll-btn:hover {
    background: #e6af00;
}
.video-gallery-body {
    background: white;
    padding: 20px;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.video-grid {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding-bottom: 10px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.video-grid::-webkit-scrollbar {
    display: none;
}
.video-card {
    min-width: 280px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background: white;
}
.video-card .video-thumb {
    position: relative;
    width: 100%;
    height: 160px;
    overflow: hidden;
    cursor: pointer;
    background: #1a1a1a;
}
.video-card .video-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.video-card .video-thumb:hover img {
    transform: scale(1.05);
}
.video-card .play-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1e3c72;
    font-size: 18px;
    pointer-events: none;
}
.video-card .video-title {
    padding: 10px 12px;
    font-size: 13px;
    color: #333;
    text-align: center;
    line-height: 1.4;
    background: #f8f9fa;
    min-height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Modal */
.video-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}
.video-modal-overlay.active {
    display: flex;
}
.video-modal {
    background: white;
    border-radius: 10px;
    width: 90%;
    max-width: 800px;
    overflow: hidden;
}
.video-modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    background: none;
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    z-index: 10;
}
.video-modal .ratio-16x9 {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
}
.video-modal .ratio-16x9 iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}
.video-modal-title {
    padding: 12px 15px;
    font-size: 14px;
    color: #333;
    text-align: center;
    background: #f8f9fa;
}

@media (max-width: 768px) {
    .video-card { min-width: 220px; }
    .video-card .video-thumb { height: 130px; }
}
</style>

<div class="video-gallery-section">
    <div class="video-gallery-header">
        <span><i class="fas fa-video"></i> Video Gallery</span>
        <div class="video-scroll-btns">
            <button class="video-scroll-btn" onclick="scrollVideos(-1)"><i class="fas fa-chevron-left"></i></button>
            <button class="video-scroll-btn" onclick="scrollVideos(1)"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
    <div class="video-gallery-body">
        <div class="video-grid" id="videoGrid">
            <?php if (!empty($videos)): ?>
                <?php foreach ($videos as $video): ?>
                <div class="video-card">
                    <div class="video-thumb" onclick="openVideoModal('https://www.youtube.com/embed/<?php echo htmlspecialchars($video['youtube_id']); ?>', '<?php echo addslashes(htmlspecialchars($video['title'])); ?>')">
                        <img src="https://img.youtube.com/vi/<?php echo htmlspecialchars($video['youtube_id']); ?>/hqdefault.jpg" 
                             alt="<?php echo htmlspecialchars($video['title']); ?>">
                        <div class="play-overlay"><i class="fas fa-play"></i></div>
                    </div>
                    <div class="video-title"><?php echo htmlspecialchars($video['title']); ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4 w-100 text-muted">
                    <i class="fas fa-video fa-3x mb-2 d-block"></i>
                    No videos available.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Video Modal -->
<div class="video-modal-overlay" id="videoModalOverlay" onclick="closeVideoModal(event)">
    <button class="video-modal-close" onclick="closeVideoModal()">&times;</button>
    <div class="video-modal">
        <div class="ratio-16x9">
            <iframe id="videoModalIframe" src="" title="Video" allowfullscreen></iframe>
        </div>
        <div class="video-modal-title" id="videoModalTitle"></div>
    </div>
</div>

<script>
function scrollVideos(direction) {
    const grid = document.getElementById('videoGrid');
    grid.scrollBy({ left: direction * 300, behavior: 'smooth' });
}

function openVideoModal(url, title) {
    document.getElementById('videoModalIframe').src = url + '?autoplay=1';
    document.getElementById('videoModalTitle').textContent = title;
    document.getElementById('videoModalOverlay').classList.add('active');
}

function closeVideoModal(event) {
    if (!event || event.target === document.getElementById('videoModalOverlay') || event.target.classList.contains('video-modal-close')) {
        document.getElementById('videoModalIframe').src = '';
        document.getElementById('videoModalOverlay').classList.remove('active');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeVideoModal();
});
</script>
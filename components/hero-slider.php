<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/HeroSlider.php';

$heroSlider = new HeroSlider();
$sliders = $heroSlider->getActiveSliders();
?>

<style>
.hero-carousel {
    position: relative;
    height: 500px;
    overflow: hidden;
    background: #1e3c72;
}

.carousel-item {
    height: 500px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.carousel-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(rgba(30, 60, 114, 0.7), rgba(30, 60, 114, 0.7));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-align: center;
    padding: 20px;
}

.carousel-content h1 {
    font-size: 48px;
    font-weight: bold;
    margin-bottom: 15px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.carousel-content p {
    font-size: 18px;
    max-width: 800px;
    margin: 0 auto;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

@media (max-width: 768px) {
    .hero-carousel, .carousel-item {
        height: 350px;
    }
    .carousel-content h1 {
        font-size: 32px;
    }
    .carousel-content p {
        font-size: 16px;
    }
}
</style>

<?php if (!empty($sliders)): ?>
<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
    <!-- Indicators -->
    <div class="carousel-indicators">
        <?php foreach ($sliders as $index => $slider): ?>
        <button type="button" 
                data-bs-target="#heroCarousel" 
                data-bs-slide-to="<?php echo $index; ?>" 
                class="<?php echo $index === 0 ? 'active' : ''; ?>" 
                aria-label="Slide <?php echo $index + 1; ?>">
        </button>
        <?php endforeach; ?>
    </div>
    
    <!-- Slides -->
    <div class="carousel-inner">
        <?php foreach ($sliders as $index => $slider): ?>
        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" 
             style="background-image: url('<?php echo htmlspecialchars($slider['image_path']); ?>');">
            <div class="carousel-overlay">
                <div class="carousel-content">
                    <h1><?php echo htmlspecialchars($slider['title']); ?></h1>
                    <?php if (!empty($slider['subtitle'])): ?>
                    <p><?php echo htmlspecialchars($slider['subtitle']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Controls -->
    <?php if (count($sliders) > 1): ?>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
    <?php endif; ?>
</div>
<?php else: ?>
<!-- Fallback if no sliders -->
<div class="hero-carousel">
    <div class="carousel-item active" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
        <div class="carousel-overlay">
            <div class="carousel-content">
                <h1>Welcome to Gyanpeeth Degree College</h1>
                <p>NAAC A+ Accredited - Quality Education Since 1964</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
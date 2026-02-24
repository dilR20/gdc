<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/HomepageCategory.php';

$homepageCategory = new HomepageCategory();
$categories = $homepageCategory->getCategories();
?>

<style>
.category-cards-section {
    background: #f8f9fa;
    padding: 40px 0;
}
.category-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-top: 4px solid #1e3c72;
    height: 100%;
    transition: transform 0.3s;
}
.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.category-card-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}
.category-icon {
    background: #1e3c72;
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-right: 15px;
    flex-shrink: 0;
}
.category-title {
    font-size: 17px;
    font-weight: 700;
    color: #1e3c72;
    margin: 0;
}
.category-items {
    list-style: none;
    padding: 0;
    margin: 0;
    min-height: 200px;
}
.category-items li {
    padding: 8px 0;
    border-bottom: 1px solid #f5f5f5;
    font-size: 13px;
    color: #555;
}
.category-items li:before {
    content: "▸";
    color: #1e3c72;
    margin-right: 8px;
    font-weight: bold;
}
.read-more-btn {
    background: #1e3c72;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 6px;
    width: 100%;
    margin-top: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
    font-size: 14px;
}
.read-more-btn:hover {
    background: #2a5298;
}
</style>

<div class="category-cards-section">
    <div class="container-fluid">
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <?php $items = $homepageCategory->getItemsByCategory($category['id'], 4); ?>
                <div class="col-lg-3 col-md-6">
                    <div class="category-card">
                        <div class="category-card-header">
                            <div class="category-icon">
                                <i class="fas fa-<?php echo htmlspecialchars($category['icon']); ?>"></i>
                            </div>
                            <h3 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                        </div>
                        <ul class="category-items">
                            <?php foreach ($items as $item): ?>
                            <li><?php echo htmlspecialchars($item['title']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button class="read-more-btn" onclick="window.location.href='category-view.php?slug=<?php echo $category['slug']; ?>'">
                            Read More
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
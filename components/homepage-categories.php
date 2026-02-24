<?php
require_once __DIR__ . '/../includes/HomepageCategory.php';

$homepageCategory = new HomepageCategory();
$categories = $homepageCategory->getCategories();
?>

<div class="homepage-categories-wrapper">
    <div class="container-fluid">
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <?php 
                $items = $homepageCategory->getItemsByCategory($category['id'], 5);
                $isEvent = $category['slug'] === 'upcoming-events';
                ?>
                
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="category-card">
                    <div class="category-header">
                        <div class="category-icon">
                            <i class="fas fa-<?php echo htmlspecialchars($category['icon']); ?>"></i>
                        </div>
                        <h3 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                    </div>
                    
                    <?php if (empty($items)): ?>
                    <div class="no-items">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">No items available</p>
                    </div>
                    <?php else: ?>
                    <ul class="category-items">
                        <?php foreach ($items as $item): ?>
                        <li class="category-item">
                            <?php if ($isEvent && $item['date']): ?>
                            <div class="event-date-badge">
                                <div class="day"><?php echo date('d', strtotime($item['date'])); ?></div>
                                <div class="month"><?php echo date('M', strtotime($item['date'])); ?></div>
                                <div class="year"><?php echo date('Y', strtotime($item['date'])); ?></div>
                            </div>
                            <?php else: ?>
                            <i class="fas fa-circle item-bullet"></i>
                            <?php endif; ?>
                            
                            <div class="item-content">
                                <div class="item-title">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                    <?php if ($item['show_new']): ?>
                                    <span class="new-tag">NEW</span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($item['link'] || $item['file_path']): ?>
                                <a href="<?php echo htmlspecialchars($item['link'] ?? $item['file_path']); ?>" 
                                   class="item-link" target="_blank">
                                    Click Here
                                </a>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <button class="read-more-btn" onclick="window.location.href='category-view.php?slug=<?php echo $category['slug']; ?>'">
                        Read More
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
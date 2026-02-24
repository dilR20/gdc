<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/LatestUpdate.php';

$model = new LatestUpdate();
$updates = $model->getActiveUpdates();
?>

<style>
.updates-ticker-section {
    background: linear-gradient(135deg, #28a745, #20c997);
    padding: 10px 0;
    overflow: hidden;
    position: relative;
    width: 100%;
}
.ticker-label {
    background: rgba(0,0,0,0.2);
    color: white;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: bold;
    border-radius: 0 20px 20px 0;
    display: inline-block;
    white-space: nowrap;
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
}
.ticker-label i {
    margin-right: 6px;
}
.ticker-wrapper {
    overflow: hidden;
    white-space: nowrap;
    padding-left: 160px;
}
.ticker-track {
    display: inline-block;
    animation: ticker-scroll 30s linear infinite;
    color: white;
    font-size: 14px;
    font-weight: 500;
}
.ticker-track a {
    color: white;
    text-decoration: none;
    transition: opacity 0.3s;
}
.ticker-track a:hover {
    opacity: 0.8;
    text-decoration: underline;
}
.ticker-separator {
    margin: 0 25px;
    opacity: 0.6;
}
@keyframes ticker-scroll {
    0% { transform: translateX(100vw); }
    100% { transform: translateX(-100%); }
}
</style>

<div class="updates-ticker-section">
    <div class="ticker-label">
        <i class="fas fa-bullhorn"></i> Latest Updates:
    </div>
    <div class="ticker-wrapper">
        <div class="ticker-track" id="tickerTrack">
            <?php
            if (!empty($updates)) {
                $first = true;
                foreach ($updates as $update) {
                    if (!$first) {
                        echo '<span class="ticker-separator">|</span>';
                    }
                    $href = '';
                    if (!empty($update['file_path'])) {
                        $href = $update['file_path'];
                    } elseif (!empty($update['link'])) {
                        $href = $update['link'];
                    }

                    if ($href) {
                        echo '<a href="' . htmlspecialchars($href) . '" target="_blank">' . htmlspecialchars($update['title']) . '</a>';
                    } else {
                        echo htmlspecialchars($update['title']);
                    }
                    $first = false;
                }
            } else {
                echo 'Welcome to ' . htmlspecialchars('Gyanpeeth Degree College, Nikashi');
            }
            ?>
        </div>
    </div>
</div>
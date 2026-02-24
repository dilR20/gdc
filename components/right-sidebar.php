<?php
require_once __DIR__ . '/../includes/Announcement.php';

$announcement = new Announcement();
$notifications = $announcement->getNotifications(10);
?>

<!-- Right Sidebar -->
<div class="right-sidebar">
    <!-- Notifications -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-bell"></i> Notification</h5>
        </div>
        <div class="list-group list-group-flush notification-list">
            <?php if (empty($notifications)): ?>
            <div class="list-group-item text-center text-muted py-4">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No notifications
            </div>
            <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                <div class="list-group-item list-group-item-action notification-item <?php echo $notif['is_important'] ? 'important' : ''; ?>">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-<?php echo htmlspecialchars($notif['icon']); ?> me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <?php if ($notif['file_path'] || $notif['link']): ?>
                            <a href="<?php echo htmlspecialchars($notif['link'] ?? $notif['file_path']); ?>" 
                               target="_blank" class="notification-link">
                                <?php echo htmlspecialchars($notif['title']); ?>
                            </a>
                            <?php else: ?>
                            <span><?php echo htmlspecialchars($notif['title']); ?></span>
                            <?php endif; ?>
                            
                            <?php if ($notif['show_new']): ?>
                            <span class="badge bg-danger ms-2 new-badge">NEW</span>
                            <?php endif; ?>
                            
                            <?php if ($notif['is_important']): ?>
                            <span class="badge bg-warning text-dark ms-2">⭐ Important</span>
                            <?php endif; ?>
                            
                            <div class="notification-date mt-1">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i>
                                    Posted on <?php echo date('Y-m-d', strtotime($notif['posted_date'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Downloads -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-download"></i> Downloads</h5>
        </div>
        <div class="list-group list-group-flush">
            <a href="downloads/prospectus-2025-26.pdf" class="list-group-item list-group-item-action" target="_blank">
                <i class="fas fa-file-pdf text-danger"></i> Prospectus 2025-26
            </a>
            <a href="downloads/faculty-appraisal.pdf" class="list-group-item list-group-item-action" target="_blank">
                <i class="fas fa-file-pdf text-danger"></i> Performance appraisal of faculty member
            </a>
            <a href="iqac.php" class="list-group-item list-group-item-action">
                <i class="fas fa-certificate text-primary"></i> IQAC
            </a>
            <a href="downloads/academic-calendar-2025-26.pdf" class="list-group-item list-group-item-action" target="_blank">
                <i class="fas fa-calendar text-success"></i> Academic Calendar 2025-26
            </a>
            <a href="downloads/quest-2023-24.pdf" class="list-group-item list-group-item-action" target="_blank">
                <i class="fas fa-book text-info"></i> Quest 2023-24
            </a>
            <a href="facilities.php" class="list-group-item list-group-item-action">
                <i class="fas fa-building text-warning"></i> Divyangjan Facilities
            </a>
            <a href="downloads/college-song.pdf" class="list-group-item list-group-item-action" target="_blank">
                <i class="fas fa-music text-danger"></i> College Song
            </a>
            <a href="syllabus.php" class="list-group-item list-group-item-action">
                <i class="fas fa-file-alt text-primary"></i> Syllabus
            </a>
        </div>
    </div>
    
    <!-- Additional Links -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-link"></i> Quick Access</h5>
        </div>
        <div class="list-group list-group-flush">
            <a href="grievance.php" class="list-group-item list-group-item-action">
                <i class="fas fa-exclamation-triangle"></i> Grievance Redressal
            </a>
            <a href="itep.php" class="list-group-item list-group-item-action">
                <i class="fas fa-chalkboard-teacher"></i> ITEP
                <span class="badge bg-danger">NEW</span>
            </a>
            <a href="best-practices.php" class="list-group-item list-group-item-action">
                <i class="fas fa-star"></i> Best Practices
            </a>
            <a href="inst-distinctiveness.php" class="list-group-item list-group-item-action">
                <i class="fas fa-award"></i> Inst. Distinctiveness
            </a>
            <a href="cell-club.php" class="list-group-item list-group-item-action">
                <i class="fas fa-users"></i> Cell/Club
            </a>
            <a href="student-corner.php" class="list-group-item list-group-item-action">
                <i class="fas fa-user-graduate"></i> Student Corner
            </a>
            <a href="activity.php" class="list-group-item list-group-item-action">
                <i class="fas fa-running"></i> Activity
            </a>
            <a href="alumni-portal.php" class="list-group-item list-group-item-action">
                <i class="fas fa-user-friends"></i> Alumni Portal
            </a>
            <a href="e-notice.php" class="list-group-item list-group-item-action">
                <i class="fas fa-bullhorn"></i> E-Notice
            </a>
            <a href="apply-certificate.php" class="list-group-item list-group-item-action">
                <i class="fas fa-certificate"></i> Apply Certificate
            </a>
            <a href="gallery.php" class="list-group-item list-group-item-action">
                <i class="fas fa-images"></i> Gallery
            </a>
            <a href="vigyanam.php" class="list-group-item list-group-item-action">
                <i class="fas fa-flask"></i> VIGYANAM
            </a>
            <a href="vikshit-bharat.php" class="list-group-item list-group-item-action">
                <i class="fas fa-flag-india"></i> Vikshit Bharat@2047
                <span class="badge bg-danger">NEW</span>
            </a>
        </div>
    </div>
</div>

<style>
/* Notification Styling */
.notification-list {
    max-height: 600px;
    overflow-y: auto;
}

.notification-item {
    border-left: 3px solid transparent;
    transition: all 0.3s;
    padding: 12px 15px;
}

.notification-item:hover {
    border-left-color: #007bff;
    background: #f8f9fa;
}

.notification-item.important {
    background: #fff3cd;
    border-left-color: #ffc107;
}

.notification-link {
    color: #333;
    text-decoration: none;
    font-weight: 500;
}

.notification-link:hover {
    color: #007bff;
    text-decoration: underline;
}

.new-badge {
    animation: blink 1s infinite;
    font-size: 9px;
    padding: 2px 6px;
}

@keyframes blink {
    0%, 50%, 100% { opacity: 1; }
    25%, 75% { opacity: 0.5; }
}

.notification-date {
    font-size: 11px;
}

/* Downloads & Links Styling */
.list-group-item {
    transition: all 0.3s;
    border-left: 3px solid transparent;
}

.list-group-item:hover {
    background: #f8f9fa;
    border-left-color: #007bff;
    padding-left: 20px;
}

.list-group-item i {
    width: 20px;
    margin-right: 10px;
}

.badge {
    font-size: 9px;
    padding: 3px 6px;
}

/* Scrollbar Styling */
.notification-list::-webkit-scrollbar {
    width: 6px;
}

.notification-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
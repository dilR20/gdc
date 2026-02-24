<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Principal.php';
require_once __DIR__ . '/../includes/IQAC.php';

$principalModel = new Principal();
$principal = $principalModel->getCurrentPrincipal();

// Get latest IQAC documents for downloads
$iqacModel = new IQAC();
$latestProspectus = $iqacModel->getLatestByCategory('prospectus');
$latestAcademic = $iqacModel->getLatestByCategory('academic');
$latestQuest = $iqacModel->getLatestByCategory('quest');
?>

<style>
.three-column-section {
    background: #f5f5f5;
    padding: 30px 0;
    width: 100%;
}
.info-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    height: 100%;
}
.info-card-header {
    background: #1e3c72;
    color: white;
    padding: 12px 20px;
    font-weight: bold;
    font-size: 16px;
}
.info-card-body {
    padding: 20px;
}
.sidebar-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.sidebar-list li {
    border-bottom: 1px solid #eee;
}
.sidebar-list a {
    display: block;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.3s;
}
.sidebar-list a:hover {
    background: #f0f0f0;
    padding-left: 25px;
    color: #1e3c72;
}
.principal-desk-short {
    max-height: 380px;
}
.principal-desk-short .principal-text {
    font-size: 14px;
    line-height: 1.6;
    max-height: 180px;
    overflow: hidden;
    text-align: justify;
}
.know-more-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.3s;
    margin-top: 15px;
}
.know-more-btn:hover {
    background: #218838;
}
.downloads-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.downloads-list li {
    border-bottom: 1px solid #eee;
}
.downloads-list a {
    display: block;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.3s;
}
.downloads-list a:hover {
    background: #f0f0f0;
    color: #1e3c72;
}
.downloads-list i {
    margin-right: 8px;
}
.downloads-list a.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

/* Modal Styles */
.modal-header {
    background: #1e3c72;
    color: white;
    border-bottom: none;
}
.modal-header .btn-close {
    filter: brightness(0) invert(1);
}
.modal-title {
    font-size: 20px;
    font-weight: bold;
}
.principal-modal-content {
    padding: 20px;
}
.principal-modal-text {
    font-size: 15px;
    line-height: 1.8;
    text-align: justify;
    color: #333;
}
.principal-modal-text p {
    margin-bottom: 15px;
}
</style>

<div class="three-column-section">
    <div class="row g-3">
        <!-- Quick Links -->
        <div class="col-lg-2 col-md-4 order-mobile-2">
            <div class="info-card">
                <div class="info-card-header">Quick Links</div>
                <div class="info-card-body p-0">
                    <ul class="sidebar-list">
                        <li><a href="#">PO PSO CO</a></li>
                        <li><a href="iqac.php">IQAC</a></li>
                        <li><a href="#">NAAC</a></li>
                        <li><a href="#">NISP</a></li>
                        <li><a href="#">DBT Star College</a></li>
                        <li><a href="#">RUSA</a></li>
                        <li><a href="#">ASTEC</a></li>
                        <li><a href="#">Unnat Bharat Abhiyan</a></li>
                        <li><a href="#">Ek Bharat Shrestha Bharat</a></li>
                        <li><a href="#">Feedback</a></li>
                        <li><a href="#">RTI DISCLOSURE</a></li>
                        <li><a href="#">Student Union</a></li>
                        <li><a href="#">Evaluation Fee <span class="badge bg-danger">NEW</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Principal's Desk -->
        <div class="col-lg-7 col-md-8 order-mobile-1">
            <div class="info-card principal-desk-short">
                <div class="info-card-header">Principal's desk</div>
                <div class="info-card-body">
                    <?php if ($principal): ?>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <?php if ($principal['photo_path']): ?>
                            <img src="<?php echo htmlspecialchars($principal['photo_path']); ?>" 
                                 class="img-fluid rounded" alt="Principal">
                            <?php else: ?>
                            <img src="https://via.placeholder.com/150x180?text=Principal" 
                                 class="img-fluid rounded" alt="Principal">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <div class="principal-text">
                                <p><?php echo htmlspecialchars(substr($principal['message'], 0, 300)); ?>...</p>
                            </div>
                            <button class="know-more-btn" data-bs-toggle="modal" data-bs-target="#principalModal">
                                Know more >>
                            </button>
                        </div>
                    </div>
                    <?php else: ?>
                    <p>Principal information not available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Downloads - UPDATED WITH DATABASE INTEGRATION -->
        <div class="col-lg-3 col-md-12 order-mobile-3">
            <div class="info-card">
                <div class="info-card-header">Downloads</div>
                <div class="info-card-body p-0">
                    <ul class="downloads-list">
                        
                        <!-- Prospectus - DYNAMIC -->
                        <?php if ($latestProspectus && !empty($latestProspectus['file_path'])): ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($latestProspectus['file_path']); ?>" target="_blank">
                                <i class="fas fa-file-pdf text-danger"></i>
                                <?php echo htmlspecialchars($latestProspectus['title']); ?>
                            </a>
                        </li>
                        <?php else: ?>
                        <li>
                            <a href="#" class="disabled">
                                <i class="fas fa-file-pdf text-danger"></i>
                                Prospectus (Coming Soon)
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <!-- Performance Appraisal - STATIC (can make dynamic if needed) -->
                        <li>
                            <a href="uploads/documents/performance-appraisal.pdf" target="_blank">
                                <i class="fas fa-file-pdf text-danger"></i>
                                Performance appraisal of faculty member
                            </a>
                        </li>
                        
                        <!-- IQAC - Link to IQAC page -->
                        <li>
                            <a href="iqac.php">
                                <i class="fas fa-certificate text-primary"></i>
                                IQAC
                            </a>
                        </li>
                        
                        <!-- Academic Calendar - DYNAMIC -->
                        <?php if ($latestAcademic && !empty($latestAcademic['file_path'])): ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($latestAcademic['file_path']); ?>" target="_blank">
                                <i class="fas fa-calendar text-success"></i>
                                <?php echo htmlspecialchars($latestAcademic['title']); ?>
                            </a>
                        </li>
                        <?php else: ?>
                        <li>
                            <a href="#" class="disabled">
                                <i class="fas fa-calendar text-success"></i>
                                Academic Calendar (Coming Soon)
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <!-- Quest - DYNAMIC -->
                        <?php if ($latestQuest && !empty($latestQuest['file_path'])): ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($latestQuest['file_path']); ?>" target="_blank">
                                <i class="fas fa-book text-info"></i>
                                <?php echo htmlspecialchars($latestQuest['title']); ?>
                            </a>
                        </li>
                        <?php else: ?>
                        <li>
                            <a href="#" class="disabled">
                                <i class="fas fa-book text-info"></i>
                                Quest (Coming Soon)
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <!-- Divyangjan Facilities - STATIC -->
                        <li>
                            <a href="uploads/documents/divyangjan-facilities.pdf" target="_blank">
                                <i class="fas fa-building text-warning"></i>
                                Divyangjan Facilities
                            </a>
                        </li>
                        
                        <!-- College Song - STATIC -->
                        <li>
                            <a href="uploads/documents/college-song.pdf" target="_blank">
                                <i class="fas fa-music text-danger"></i>
                                College Song
                            </a>
                        </li>
                        
                        <!-- Syllabus - STATIC or can link to syllabus page -->
                        <li>
                            <a href="syllabus.php">
                                <i class="fas fa-file-alt text-primary"></i>
                                Syllabus
                            </a>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Principal Modal -->
<?php if ($principal): ?>
<div class="modal fade" id="principalModal" tabindex="-1" aria-labelledby="principalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="principalModalLabel">Principal's desk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="principal-modal-content">
                    <div class="principal-modal-text">
                        <?php 
                        // Split message into paragraphs for better formatting
                        $paragraphs = explode("\n", $principal['message']);
                        foreach ($paragraphs as $para) {
                            if (trim($para)) {
                                echo '<p>' . htmlspecialchars($para) . '</p>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
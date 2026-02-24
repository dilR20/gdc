<?php
require_once __DIR__ . '/../includes/Principal.php';

$principalModel = new Principal();
$principal = $principalModel->getCurrentPrincipal();
?>

<?php if ($principal): ?>
<div class="principal-section card mb-4">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fas fa-user-tie"></i> Principal's Desk</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center">
                <?php if ($principal['photo_path']): ?>
                <img src="<?php echo htmlspecialchars($principal['photo_path']); ?>" 
                     alt="Principal" class="img-fluid rounded shadow">
                <?php else: ?>
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                     style="height: 200px; border-radius: 8px;">
                    <i class="fas fa-user fa-4x"></i>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-9">
                <h5 class="mb-2"><?php echo htmlspecialchars($principal['name']); ?></h5>
                <p class="text-muted mb-3">
                    <strong><?php echo htmlspecialchars($principal['designation']); ?></strong>
                </p>
                
                <?php if ($principal['qualification']): ?>
                <p class="small mb-3">
                    <i class="fas fa-graduation-cap text-primary"></i>
                    <?php echo htmlspecialchars($principal['qualification']); ?>
                </p>
                <?php endif; ?>
                
                <?php if ($principal['message']): ?>
                <p class="text-justify">
                    <?php echo nl2br(htmlspecialchars(substr($principal['message'], 0, 400))); ?>...
                </p>
                <?php endif; ?>
                
                <a href="principal-desk.php" class="btn btn-primary btn-sm">
                    Read More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
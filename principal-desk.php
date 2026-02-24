<?php
require_once 'config/config.php';
require_once 'includes/Principal.php';

$principalModel = new Principal();
$principal = $principalModel->getCurrentPrincipal();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal's Desk | BN College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/department.css">
    
    <style>
        .principal-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 60px 0;
        }
        .principal-photo {
            width: 100%;
            max-width: 300px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .principal-card {
            margin-top: -80px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
        }
        .message-section {
            background: #f8f9fa;
            padding: 30px;
            border-left: 5px solid #2a5298;
            border-radius: 8px;
            margin: 30px 0;
        }
        .contact-item {
            padding: 15px;
            background: white;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #2a5298;
        }
    </style>
</head>
<body>
    <!-- Navigation (include your existing navigation here) -->
    
    <!-- Header -->
    <section class="principal-header">
        <div class="container">
            <div class="text-center">
                <h1 class="display-4 mb-3">Principal's Desk</h1>
                <p class="lead">Leadership & Vision</p>
            </div>
        </div>
    </section>
    
    <!-- Principal Information -->
    <?php if ($principal): ?>
    <section class="py-5">
        <div class="container">
            <div class="principal-card">
                <div class="row">
                    <!-- Photo Column -->
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        <?php if ($principal['photo_path']): ?>
                        <img src="<?php echo htmlspecialchars($principal['photo_path']); ?>" 
                             alt="<?php echo htmlspecialchars($principal['name']); ?>"
                             class="principal-photo">
                        <?php else: ?>
                        <div class="principal-photo bg-secondary d-flex align-items-center justify-content-center mx-auto">
                            <i class="fas fa-user fa-5x text-white"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Information Column -->
                    <div class="col-md-8">
                        <h2 class="mb-3"><?php echo htmlspecialchars($principal['name']); ?></h2>
                        <h5 class="text-muted mb-4"><?php echo htmlspecialchars($principal['designation']); ?></h5>
                        
                        <?php if ($principal['qualification']): ?>
                        <div class="mb-3">
                            <h6><i class="fas fa-graduation-cap text-primary"></i> Qualification</h6>
                            <p><?php echo nl2br(htmlspecialchars($principal['qualification'])); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Contact Information -->
                        <div class="row mt-4">
                            <?php if ($principal['email']): ?>
                            <div class="col-md-6">
                                <div class="contact-item">
                                    <i class="fas fa-envelope text-primary"></i>
                                    <strong class="d-block">Email</strong>
                                    <a href="mailto:<?php echo htmlspecialchars($principal['email']); ?>">
                                        <?php echo htmlspecialchars($principal['email']); ?>
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($principal['phone']): ?>
                            <div class="col-md-6">
                                <div class="contact-item">
                                    <i class="fas fa-phone text-primary"></i>
                                    <strong class="d-block">Phone</strong>
                                    <?php echo htmlspecialchars($principal['phone']); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Message Section -->
                <?php if ($principal['message']): ?>
                <div class="message-section mt-4">
                    <h4 class="mb-3">
                        <i class="fas fa-quote-left text-primary"></i> Message from the Principal
                    </h4>
                    <p class="lead"><?php echo nl2br(htmlspecialchars($principal['message'])); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Achievements Section -->
                <?php if ($principal['achievements']): ?>
                <div class="mt-4">
                    <h4 class="mb-3">
                        <i class="fas fa-trophy text-primary"></i> Achievements
                    </h4>
                    <p><?php echo nl2br(htmlspecialchars($principal['achievements'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php else: ?>
    <section class="py-5">
        <div class="container">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h4>Principal information will be updated soon.</h4>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Back to Home -->
    <section class="py-4 bg-light">
        <div class="container text-center">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Back to Home
            </a>
        </div>
    </section>
    
    <!-- Footer (include your existing footer here) -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

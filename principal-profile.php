<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/Principal.php';
#require_once __DIR__ . '/includes/FileEncrypt.php';

$principalModel = new Principal();
$principal = $principalModel->getCurrentPrincipal();
$education = $principalModel->getEducation();
$booksPublished = $principalModel->getBooksPublished();
$booksEditor = $principalModel->getBooksAsEditor();
$researchProjects = $principalModel->getResearchProjects();
$pubResearch = $principalModel->getPubResearch();
$pubBooks = $principalModel->getPubBooks();
$pubConference = $principalModel->getPubConference();
$programmes = $principalModel->getProgrammes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal's Profile - Gyanpeeth Degree College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .page-banner {
            background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
                        url('assets/images/banner-bg.jpg') center/cover no-repeat;
            padding: 60px 0 40px;
            color: white;
            min-height: 160px;
            display: flex;
            align-items: center;
        }
        .page-banner h1 { font-size: 32px; font-weight: bold; margin: 0 0 8px; }
        .breadcrumb a { color: #ffc107; text-decoration: none; font-size: 13px; }
        .breadcrumb-item.active { color: #ccc; font-size: 13px; }
        .breadcrumb-item + .breadcrumb-item::before { color: #aaa; content: "›"; }

        .profile-page { padding: 40px 0; background: #f8f9fa; }

        .profile-sidebar { position: sticky; top: 20px; }
        .profile-photo-wrap {
            border-radius: 12px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15); margin-bottom: 20px;
        }
        .profile-photo-wrap img { width: 100%; display: block; }
        .profile-name { text-align: center; margin-bottom: 20px; }
        .profile-name h2 { font-size: 20px; font-weight: bold; color: #1e3c72; margin: 0 0 4px; }
        .profile-name p { color: #777; font-size: 14px; margin: 0; }

        .contact-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .contact-item { display: flex; align-items: center; padding: 14px 18px; border-bottom: 1px solid #f0f0f0; gap: 14px; }
        .contact-item:last-child { border-bottom: none; }
        .contact-icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .contact-icon.phone { background: #fff3cd; color: #ffc107; }
        .contact-icon.email { background: #d1ecf1; color: #17a2b8; }
        .contact-icon.pdf   { background: #d4edda; color: #28a745; }
        .contact-text { font-size: 13px; color: #444; }
        .contact-text a { color: #1e3c72; text-decoration: none; font-weight: 500; }
        .contact-text a:hover { text-decoration: underline; }

        .profile-content { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 30px; }
        .section-title { font-size: 18px; font-weight: bold; color: #1e3c72; margin: 28px 0 12px; padding-bottom: 8px; border-bottom: 2px solid #1e3c72; display: inline-block; }
        .section-title:first-child { margin-top: 0; }

        .edu-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .edu-table thead { background: #1e3c72; color: white; }
        .edu-table th { text-align: left; padding: 10px 14px; font-size: 13px; font-weight: 600; }
        .edu-table td { padding: 10px 14px; font-size: 13px; color: #444; border-bottom: 1px solid #eee; }
        .edu-table tr:hover td { background: #f8f9fa; }

        .exp-text { font-size: 14px; color: #444; line-height: 1.7; margin: 8px 0; }

        .books-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 10px; }
        .book-item { background: #f8f9fa; border-radius: 8px; padding: 14px 16px; font-size: 13px; color: #444; line-height: 1.6; display: flex; gap: 10px; }
        .book-item .book-icon { color: #28a745; font-size: 16px; flex-shrink: 0; margin-top: 2px; }

        .project-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .project-table thead { background: #1e3c72; color: white; }
        .project-table th { text-align: left; padding: 10px 12px; font-size: 12px; font-weight: 600; }
        .project-table td { padding: 10px 12px; font-size: 12px; color: #444; border-bottom: 1px solid #eee; vertical-align: top; }
        .project-table tr:hover td { background: #f8f9fa; }

        .pub-tabs { display: flex; gap: 0; margin-top: 10px; border-bottom: 2px solid #dee2e6; flex-wrap: wrap; }
        .pub-tab { padding: 10px 18px; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #666; background: #f0f0f0; border: 1px solid #dee2e6; border-bottom: none; cursor: pointer; transition: all 0.3s; letter-spacing: 0.5px; }
        .pub-tab:first-child { border-radius: 6px 0 0 0; }
        .pub-tab:last-child  { border-radius: 0 6px 6px 0; }
        .pub-tab:hover, .pub-tab.active { background: #1e3c72; color: white; }
        .pub-content { display: none; padding: 18px 0; }
        .pub-content.active { display: block; }

        .programme-section { margin-top: 20px; }
        .programme-section h5 { font-size: 15px; font-weight: bold; color: #1e3c72; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 2px solid #ffc107; display: inline-block; }
        .programme-item { display: flex; gap: 10px; padding: 8px 0; font-size: 13px; color: #444; line-height: 1.5; border-bottom: 1px solid #f0f0f0; }
        .programme-item:last-child { border-bottom: none; }
        .programme-item .prog-icon { color: #1e3c72; font-size: 10px; flex-shrink: 0; margin-top: 5px; }

        .empty-msg { color: #999; font-style: italic; font-size: 13px; padding: 10px 0; }

        @media (max-width: 768px) {
            .books-grid { grid-template-columns: 1fr; }
            .profile-content { padding: 20px; }
            .page-banner { padding: 40px 0 25px; }
            .page-banner h1 { font-size: 24px; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/components/header.php'; ?>
<?php include __DIR__ . '/components/navigation.php'; ?>

<!-- Banner -->
<div class="page-banner">
    <div class="container">
        <h1>Principal's Profile</h1>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Administration</a></li>
                <li class="breadcrumb-item active">Principal</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Profile -->
<div class="profile-page">
    <div class="container">
        <?php if ($principal): ?>
        <div class="row">

            <!-- LEFT SIDEBAR -->
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="profile-sidebar">
                    <div class="profile-photo-wrap">
                        <?php if ($principal['photo_path']): ?>
                            <img src="<?php echo htmlspecialchars($principal['photo_path']); ?>" alt="Principal Photo">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x350?text=Photo" alt="Principal Photo">
                        <?php endif; ?>
                    </div>
                    <div class="profile-name">
                        <h2><?php echo htmlspecialchars($principal['name'] ?? 'Principal'); ?></h2>
                        <p><?php echo htmlspecialchars($principal['designation'] ?? 'Principal'); ?></p>
                    </div>
                    <div class="contact-card">
                        <?php if (!empty($principal['phone'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon phone"><i class="fas fa-phone"></i></div>
                            <div class="contact-text"><a href="tel:<?php echo htmlspecialchars($principal['phone']); ?>"><?php echo htmlspecialchars($principal['phone']); ?></a></div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($principal['email'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon email"><i class="fas fa-envelope"></i></div>
                            <div class="contact-text"><a href="mailto:<?php echo htmlspecialchars($principal['email']); ?>"><?php echo htmlspecialchars($principal['email']); ?></a></div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($principal['profile_pdf'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon pdf"><i class="fas fa-file-download"></i></div>
                            <div class="contact-text"><a href="<?php echo htmlspecialchars($principal['profile_pdf']); ?>" target="_blank">Profile</a></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="col-lg-9 col-md-8">
                <div class="profile-content">

                    <!-- EDUCATION -->
                    <h3 class="section-title">Education Qualification</h3>
                    <?php if (!empty($education)): ?>
                    <table class="edu-table">
                        <thead>
                            <tr><th>Degree</th><th>Board/University</th><th>Year</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($education as $edu): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($edu['degree'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($edu['board'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($edu['year'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="empty-msg">No education records available.</p>
                    <?php endif; ?>

                    <!-- TEACHING EXPERIENCE -->
                    <h3 class="section-title">Teaching Experience:</h3>
                    <?php if (!empty($principal['teaching_exp'])): ?>
                    <p class="exp-text"><?php echo htmlspecialchars($principal['teaching_exp']); ?></p>
                    <?php else: ?>
                    <p class="empty-msg">No teaching experience recorded.</p>
                    <?php endif; ?>

                    <!-- ADMINISTRATIVE EXPERIENCE -->
                    <h3 class="section-title">Administrative Experience</h3>
                    <?php if (!empty($principal['admin_exp'])): ?>
                    <p class="exp-text"><?php echo htmlspecialchars($principal['admin_exp']); ?></p>
                    <?php else: ?>
                    <p class="empty-msg">No administrative experience recorded.</p>
                    <?php endif; ?>

                    <!-- BOOKS PUBLISHED -->
                    <h3 class="section-title">Book Published</h3>
                    <?php if (!empty($booksPublished)): ?>
                    <div class="books-grid">
                        <?php foreach ($booksPublished as $book): ?>
                        <div class="book-item">
                            <i class="fas fa-check-circle book-icon"></i>
                            <div><?php echo htmlspecialchars($book['title'] ?? ''); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="empty-msg">No books published.</p>
                    <?php endif; ?>

                    <!-- BOOKS AS EDITOR -->
                    <h3 class="section-title">Book Published As Editor:</h3>
                    <?php if (!empty($booksEditor)): ?>
                    <?php foreach ($booksEditor as $book): ?>
                    <div class="book-item" style="margin-top:10px;">
                        <i class="fas fa-check-circle book-icon"></i>
                        <div><?php echo htmlspecialchars($book['title'] ?? ''); ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p class="empty-msg">No books edited.</p>
                    <?php endif; ?>

                    <!-- RESEARCH PROJECTS -->
                    <h3 class="section-title">Sponsored Research Projects:</h3>
                    <?php if (!empty($researchProjects)): ?>
                    <table class="project-table">
                        <thead>
                            <tr>
                                <th style="width:40px">Sl.no.</th>
                                <th>Title</th>
                                <th style="width:100px">Sponsored</th>
                                <th>Period</th>
                                <th style="width:80px">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($researchProjects as $project): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($project['slno'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($project['title'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($project['sponsored'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($project['period'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($project['amount'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="empty-msg">No research projects recorded.</p>
                    <?php endif; ?>

                    <!-- PUBLICATIONS TABS -->
                    <h3 class="section-title">Publications</h3>
                    <div class="pub-tabs">
                        <div class="pub-tab active" onclick="switchTab('research')">Research Paper Published</div>
                        <div class="pub-tab" onclick="switchTab('books')">Books</div>
                        <div class="pub-tab" onclick="switchTab('conference')">Conference</div>
                        <div class="pub-tab" onclick="switchTab('oprcs')">OP/RC/STC/FDP</div>
                    </div>

                    <!-- Research Papers Tab -->
                    <div class="pub-content active" id="tab-research">
                        <?php if (!empty($pubResearch)): ?>
                        <?php foreach ($pubResearch as $item): ?>
                        <div class="programme-item">
                            <i class="fas fa-file-alt prog-icon" style="font-size:14px;"></i>
                            <div><?php echo htmlspecialchars($item['text'] ?? ''); ?></div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="empty-msg">No research papers recorded.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Books Tab -->
                    <div class="pub-content" id="tab-books">
                        <?php if (!empty($pubBooks)): ?>
                        <?php foreach ($pubBooks as $item): ?>
                        <div class="programme-item">
                            <i class="fas fa-book prog-icon" style="font-size:14px;"></i>
                            <div><?php echo htmlspecialchars($item['text'] ?? ''); ?></div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="empty-msg">No books recorded.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Conference Tab -->
                    <div class="pub-content" id="tab-conference">
                        <?php if (!empty($pubConference)): ?>
                        <?php foreach ($pubConference as $item): ?>
                        <div class="programme-item">
                            <i class="fas fa-users prog-icon" style="font-size:14px;"></i>
                            <div><?php echo htmlspecialchars($item['text'] ?? ''); ?></div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="empty-msg">No conference records.</p>
                        <?php endif; ?>
                    </div>

                    <!-- OP/RC/STC/FDP Tab -->
                    <div class="pub-content" id="tab-oprcs">
                        <?php if (!empty($programmes)): ?>
                        <?php foreach ($programmes as $prog): ?>
                        <div class="programme-section">
                            <h5><?php echo htmlspecialchars($prog['title'] ?? ''); ?></h5>
                            <?php if (!empty($prog['items'])): ?>
                            <?php foreach ($prog['items'] as $item): ?>
                            <div class="programme-item">
                                <i class="fas fa-circle prog-icon"></i>
                                <div><?php echo htmlspecialchars($item['text'] ?? ''); ?></div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="empty-msg">No programme records.</p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- No principal found -->
        <div class="text-center py-5">
            <i class="fas fa-user-tie fa-5x text-muted mb-3 d-block"></i>
            <h4 class="text-muted">Principal information is not available at this time.</h4>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function switchTab(tabName) {
    document.querySelectorAll('.pub-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.pub-content').forEach(c => c.classList.remove('active'));
    const tabs = document.querySelectorAll('.pub-tab');
    const tabMap = { research: 0, books: 1, conference: 2, oprcs: 3 };
    tabs[tabMap[tabName]].classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}
</script>
</body>
</html>
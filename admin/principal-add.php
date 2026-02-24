<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Principal.php';

$auth = new Auth();
$auth->requireLogin();

$model = new Principal();
$isEdit = isset($_GET['edit']);
$principal = null;
$error = '';

if ($isEdit) {
    $principal = $model->getById($_GET['edit']);
    if (!$principal) {
        header('Location: principal-list.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name'           => trim($_POST['name'] ?? ''),
        'designation'    => trim($_POST['designation'] ?? 'Principal'),
        'phone'          => trim($_POST['phone'] ?? ''),
        'email'          => trim($_POST['email'] ?? ''),
        'message'        => trim($_POST['message'] ?? ''),
        'joining_date'   => !empty($_POST['joining_date']) ? $_POST['joining_date'] : null,
        'achievements'   => trim($_POST['achievements'] ?? ''),
        'is_current'     => isset($_POST['is_current']) ? 1 : 0,
        'teaching_exp'   => trim($_POST['teaching_exp'] ?? ''),
        'admin_exp'      => trim($_POST['admin_exp'] ?? ''),
    ];

    // Keep existing files by default on edit
    if ($isEdit && $principal) {
        $data['photo_path']  = $principal['photo_path'];
        $data['profile_pdf'] = $principal['profile_pdf'];
    }

    // Validate
    if (empty($data['name'])) {
        $error = 'Name is required.';
    }

    // Handle remove photo
    if (isset($_POST['remove_photo']) && $isEdit) {
        $model->deletePhotoFile($_GET['edit']);
        $data['photo_path'] = null;
    }

    // Handle remove PDF
    if (isset($_POST['remove_pdf']) && $isEdit) {
        $model->deletePdfFile($_GET['edit']);
        $data['profile_pdf'] = null;
    }

    // Handle photo upload
    $photoResult = $model->handlePhotoUpload();
    if ($photoResult && isset($photoResult['error'])) {
        $error = $photoResult['error'];
    } elseif ($photoResult) {
        if ($isEdit) $model->deletePhotoFile($_GET['edit']);
        $data['photo_path'] = $photoResult['file_path'];
    }

    // Handle PDF upload
    $pdfResult = $model->handlePdfUpload();
    if ($pdfResult && isset($pdfResult['error'])) {
        $error = $pdfResult['error'];
    } elseif ($pdfResult) {
        if ($isEdit) $model->deletePdfFile($_GET['edit']);
        $data['profile_pdf'] = $pdfResult['file_path'];
    }

    // JSON fields
    $data['education']          = buildEducationJson();
    $data['books_published']    = buildBooksJson('book_pub');
    $data['books_as_editor']    = buildBooksJson('book_editor');
    $data['research_projects']  = buildResearchJson();
    $data['publications']       = buildSimpleJson('pub_research');
    $data['pub_books']          = buildSimpleJson('pub_books');
    $data['pub_conference']     = buildSimpleJson('pub_conference');
    $data['programmes']         = buildProgrammesJson();

    if (empty($error)) {
        if ($isEdit) {
            $model->update($_GET['edit'], $data);
        } else {
            $model->create($data);
        }
        header('Location: principal-list.php?saved=1');
        exit();
    }
}

// ============================
// JSON BUILDER FUNCTIONS
// ============================

function buildEducationJson() {
    $rows = [];
    if (isset($_POST['edu_degree']) && is_array($_POST['edu_degree'])) {
        foreach ($_POST['edu_degree'] as $i => $degree) {
            if (!empty(trim($degree))) {
                $rows[] = [
                    'degree' => trim($degree),
                    'board'  => trim($_POST['edu_board'][$i] ?? ''),
                    'year'   => trim($_POST['edu_year'][$i] ?? '')
                ];
            }
        }
    }
    return !empty($rows) ? json_encode($rows) : null;
}

function buildBooksJson($prefix) {
    $rows = [];
    if (isset($_POST[$prefix . '_title']) && is_array($_POST[$prefix . '_title'])) {
        foreach ($_POST[$prefix . '_title'] as $title) {
            if (!empty(trim($title))) {
                $rows[] = ['title' => trim($title)];
            }
        }
    }
    return !empty($rows) ? json_encode($rows) : null;
}

function buildResearchJson() {
    $rows = [];
    if (isset($_POST['res_title']) && is_array($_POST['res_title'])) {
        foreach ($_POST['res_title'] as $i => $title) {
            if (!empty(trim($title))) {
                $rows[] = [
                    'slno'      => (string)($i + 1),
                    'title'     => trim($title),
                    'sponsored' => trim($_POST['res_sponsored'][$i] ?? ''),
                    'period'    => trim($_POST['res_period'][$i] ?? ''),
                    'amount'    => trim($_POST['res_amount'][$i] ?? '')
                ];
            }
        }
    }
    return !empty($rows) ? json_encode($rows) : null;
}

function buildSimpleJson($prefix) {
    $rows = [];
    if (isset($_POST[$prefix . '_text']) && is_array($_POST[$prefix . '_text'])) {
        foreach ($_POST[$prefix . '_text'] as $text) {
            if (!empty(trim($text))) {
                $rows[] = ['text' => trim($text)];
            }
        }
    }
    return !empty($rows) ? json_encode($rows) : null;
}

function buildProgrammesJson() {
    $programmes = [];
    $types = ['orientation', 'refresher', 'stc_fdp'];
    $titles = [
        'orientation' => 'Orientation Programme(OP):',
        'refresher'   => 'Refresher Course (RC):',
        'stc_fdp'     => 'Short Term Course / Faculty Development Programme:'
    ];

    foreach ($types as $type) {
        $key = 'prog_' . $type;
        if (isset($_POST[$key]) && is_array($_POST[$key])) {
            $items = [];
            foreach ($_POST[$key] as $text) {
                if (!empty(trim($text))) {
                    $items[] = ['text' => trim($text)];
                }
            }
            if (!empty($items)) {
                $programmes[] = [
                    'type'  => $type,
                    'title' => $titles[$type],
                    'items' => $items
                ];
            }
        }
    }
    return !empty($programmes) ? json_encode($programmes) : null;
}

// ============================
// DECODE EXISTING JSON FOR EDIT
// ============================
$education         = $principal ? Principal::jsonDecode($principal['education']) : [];
$booksPublished    = $principal ? Principal::jsonDecode($principal['books_published']) : [];
$booksEditor       = $principal ? Principal::jsonDecode($principal['books_as_editor']) : [];
$researchProjects  = $principal ? Principal::jsonDecode($principal['research_projects']) : [];
$pubResearch       = $principal ? Principal::jsonDecode($principal['publications']) : [];
$pubBooks          = $principal ? Principal::jsonDecode($principal['pub_books']) : [];
$pubConference     = $principal ? Principal::jsonDecode($principal['pub_conference']) : [];
$programmes        = $principal ? Principal::jsonDecode($principal['programmes']) : [];

// Extract programme items by type
$progOrientation = [];
$progRefresher   = [];
$progStcFdp      = [];
foreach ($programmes as $prog) {
    switch ($prog['type'] ?? '') {
        case 'orientation': $progOrientation = $prog['items'] ?? []; break;
        case 'refresher':   $progRefresher   = $prog['items'] ?? []; break;
        case 'stc_fdp':     $progStcFdp      = $prog['items'] ?? []; break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Principal - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .main-content { margin-left: 260px; padding: 30px; }

        .section-box {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 25px;
            overflow: hidden;
        }
        .section-box-header {
            background: #1e3c72;
            color: white;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-box-header .add-btn {
            background: #ffc107;
            color: #1e3c72;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }
        .section-box-header .add-btn:hover { background: #e6af00; }
        .section-box-body { padding: 20px; }

        .dynamic-row {
            background: #f8f9fa;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 10px;
            position: relative;
        }
        .dynamic-row:hover { border-color: #1e3c72; }
        .remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #dc3545;
            color: white;
            border: none;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remove-btn:hover { background: #c82333; }

        .photo-preview-wrap {
            width: 120px;
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #dee2e6;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-preview-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .existing-file-box {
            background: #f0fff4;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 10px;
        }
        .tab-nav {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #dee2e6;
            flex-wrap: wrap;
        }
        .tab-nav-btn {
            padding: 9px 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            background: #f0f0f0;
            border: 1px solid #dee2e6;
            border-bottom: none;
            cursor: pointer;
            color: #666;
            transition: all 0.2s;
        }
        .tab-nav-btn:first-child { border-radius: 5px 0 0 0; }
        .tab-nav-btn:last-child  { border-radius: 0 5px 5px 0; }
        .tab-nav-btn:hover, .tab-nav-btn.active { background: #1e3c72; color: white; }
        .tab-panel { display: none; padding: 18px 0; }
        .tab-panel.active { display: block; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i> <?php echo $isEdit ? 'Edit' : 'Add'; ?> Principal</h1>
                <a href="principal-list.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <!-- Error -->
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <!-- ====== BASIC INFO ====== -->
                <div class="section-box">
                    <div class="section-box-header"><span><i class="fas fa-user"></i> Basic Information</span></div>
                    <div class="section-box-body">
                        <div class="row g-3">
                            <!-- Photo -->
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Photo</label>
                                <div class="photo-preview-wrap mb-2" id="photoPreview">
                                    <?php if ($principal && $principal['photo_path']): ?>
                                    <img src="../<?php echo htmlspecialchars($principal['photo_path']); ?>" alt="Photo" id="photoImg">
                                    <?php else: ?>
                                    <i class="fas fa-camera fa-2x text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control form-control-sm" name="principal_photo" accept=".jpg,.jpeg,.png,.gif" onchange="previewPhoto(this)">
                                <small class="text-muted">Max 5MB. JPG/PNG/GIF</small>
                                <?php if ($principal && $principal['photo_path']): ?>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="remove_photo" id="removePhoto">
                                    <label class="form-check-label text-danger small" for="removePhoto"><i class="fas fa-trash"></i> Remove</label>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Fields -->
                            <div class="col-md-10">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Full Name *</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo $principal ? htmlspecialchars($principal['name']) : ''; ?>" placeholder="e.g. DR. KISHOR KR SHAH" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Designation</label>
                                        <input type="text" class="form-control" name="designation" value="<?php echo $principal ? htmlspecialchars($principal['designation']) : 'Principal'; ?>" placeholder="e.g. Principal">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Phone</label>
                                        <input type="text" class="form-control" name="phone" value="<?php echo $principal ? htmlspecialchars($principal['phone']) : ''; ?>" placeholder="03662 295026">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo $principal ? htmlspecialchars($principal['email']) : ''; ?>" placeholder="email@example.com">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Joining Date</label>
                                        <input type="date" class="form-control" name="joining_date" value="<?php echo $principal ? htmlspecialchars($principal['joining_date'] ?? '') : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Profile PDF</label>
                                        <?php if ($principal && $principal['profile_pdf']): ?>
                                        <div class="existing-file-box d-flex align-items-center justify-content-between">
                                            <span class="small"><i class="fas fa-file-pdf text-danger me-2"></i> Profile PDF exists</span>
                                            <a href="../serve-file.php?token=<?php echo urlencode(require_once('../includes/FileEncrypt.php') ?: ''); ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="remove_pdf" id="removePdf">
                                            <label class="form-check-label text-danger small" for="removePdf"><i class="fas fa-trash"></i> Remove PDF</label>
                                        </div>
                                        <label class="form-label small text-muted">Or upload new PDF:</label>
                                        <?php endif; ?>
                                        <input type="file" class="form-control form-control-sm" name="principal_pdf" accept=".pdf">
                                        <small class="text-muted">Max 10MB. PDF only</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Status</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_current" <?php echo (!$principal || $principal['is_current']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Set as Current Principal</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ====== MESSAGE ====== -->
                <div class="section-box">
                    <div class="section-box-header"><span><i class="fas fa-comment-alt"></i> Principal's Message</span></div>
                    <div class="section-box-body">
                        <textarea class="form-control" name="message" rows="5" placeholder="Enter the principal's message for the homepage..."><?php echo $principal ? htmlspecialchars($principal['message'] ?? '') : ''; ?></textarea>
                    </div>
                </div>

                <!-- ====== ACHIEVEMENTS ====== -->
                <div class="section-box">
                    <div class="section-box-header"><span><i class="fas fa-trophy"></i> Achievements</span></div>
                    <div class="section-box-body">
                        <textarea class="form-control" name="achievements" rows="3" placeholder="Enter achievements..."><?php echo $principal ? htmlspecialchars($principal['achievements'] ?? '') : ''; ?></textarea>
                    </div>
                </div>

                <!-- ====== EDUCATION ====== -->
                <div class="section-box">
                    <div class="section-box-header">
                        <span><i class="fas fa-graduation-cap"></i> Education Qualification</span>
                        <button type="button" class="add-btn" onclick="addRow('education')"><i class="fas fa-plus"></i> Add Row</button>
                    </div>
                    <div class="section-box-body" id="education-container">
                        <?php
                        $eduRows = !empty($education) ? $education : [['degree'=>'','board'=>'','year'=>'']];
                        foreach ($eduRows as $edu):
                        ?>
                        <div class="dynamic-row">
                            <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Degree</label>
                                    <input type="text" class="form-control form-control-sm" name="edu_degree[]" value="<?php echo htmlspecialchars($edu['degree'] ?? ''); ?>" placeholder="e.g. Ph.D, Chemistry">
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label small fw-bold">Board / University</label>
                                    <input type="text" class="form-control form-control-sm" name="edu_board[]" value="<?php echo htmlspecialchars($edu['board'] ?? ''); ?>" placeholder="e.g. Gauhati University, Assam">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">Year</label>
                                    <input type="text" class="form-control form-control-sm" name="edu_year[]" value="<?php echo htmlspecialchars($edu['year'] ?? ''); ?>" placeholder="2013">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ====== EXPERIENCE ====== -->
                <div class="section-box">
                    <div class="section-box-header"><span><i class="fas fa-briefcase"></i> Experience</span></div>
                    <div class="section-box-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Teaching Experience</label>
                            <textarea class="form-control" name="teaching_exp" rows="2" placeholder="e.g. Associate Professor in Chemistry, ADP College, Nagaon, Assam (2005 – 2024)"><?php echo $principal ? htmlspecialchars($principal['teaching_exp'] ?? '') : ''; ?></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Administrative Experience</label>
                            <textarea class="form-control" name="admin_exp" rows="2" placeholder="e.g. Principal, B N College, Dhubri, Assam (18th November, 2024 – till date)"><?php echo $principal ? htmlspecialchars($principal['admin_exp'] ?? '') : ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- ====== BOOKS PUBLISHED ====== -->
                <div class="section-box">
                    <div class="section-box-header">
                        <span><i class="fas fa-book"></i> Books Published</span>
                        <button type="button" class="add-btn" onclick="addRow('book_pub')"><i class="fas fa-plus"></i> Add Book</button>
                    </div>
                    <div class="section-box-body" id="book_pub-container">
                        <?php
                        $bpRows = !empty($booksPublished) ? $booksPublished : [['title'=>'']];
                        foreach ($bpRows as $book):
                        ?>
                        <div class="dynamic-row">
                            <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                            <label class="form-label small fw-bold">Book Title / Details</label>
                            <input type="text" class="form-control form-control-sm" name="book_pub_title[]" value="<?php echo htmlspecialchars($book['title'] ?? ''); ?>" placeholder="e.g. Fundamentals of Spectroscopy, ISBN ..., Year: 2018, Publisher...">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ====== BOOKS AS EDITOR ====== -->
                <div class="section-box">
                    <div class="section-box-header">
                        <span><i class="fas fa-book-open"></i> Books Published As Editor</span>
                        <button type="button" class="add-btn" onclick="addRow('book_editor')"><i class="fas fa-plus"></i> Add Book</button>
                    </div>
                    <div class="section-box-body" id="book_editor-container">
                        <?php
                        $beRows = !empty($booksEditor) ? $booksEditor : [['title'=>'']];
                        foreach ($beRows as $book):
                        ?>
                        <div class="dynamic-row">
                            <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                            <label class="form-label small fw-bold">Book Title / Details</label>
                            <input type="text" class="form-control form-control-sm" name="book_editor_title[]" value="<?php echo htmlspecialchars($book['title'] ?? ''); ?>" placeholder="e.g. Proceedings of National Seminar..., ISBN ...">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ====== RESEARCH PROJECTS ====== -->
                <div class="section-box">
                    <div class="section-box-header">
                        <span><i class="fas fa-microscope"></i> Sponsored Research Projects</span>
                        <button type="button" class="add-btn" onclick="addRow('research')"><i class="fas fa-plus"></i> Add Project</button>
                    </div>
                    <div class="section-box-body" id="research-container">
                        <?php
                        $rpRows = !empty($researchProjects) ? $researchProjects : [['title'=>'','sponsored'=>'','period'=>'','amount'=>'']];
                        foreach ($rpRows as $proj):
                        ?>
                        <div class="dynamic-row">
                            <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold">Project Title</label>
                                    <input type="text" class="form-control form-control-sm" name="res_title[]" value="<?php echo htmlspecialchars($proj['title'] ?? ''); ?>" placeholder="Project title...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">Sponsored By</label>
                                    <input type="text" class="form-control form-control-sm" name="res_sponsored[]" value="<?php echo htmlspecialchars($proj['sponsored'] ?? ''); ?>" placeholder="UGC, New Delhi">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Period / Grant No.</label>
                                    <input type="text" class="form-control form-control-sm" name="res_period[]" value="<?php echo htmlspecialchars($proj['period'] ?? ''); ?>" placeholder="F.5-292/2007...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">Amount</label>
                                    <input type="text" class="form-control form-control-sm" name="res_amount[]" value="<?php echo htmlspecialchars($proj['amount'] ?? ''); ?>" placeholder="Rs. 80,000">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ====== PUBLICATIONS ====== -->
                <div class="section-box">
                    <div class="section-box-header"><span><i class="fas fa-newspaper"></i> Publications</span></div>
                    <div class="section-box-body">

                        <!-- Tab Nav -->
                        <div class="tab-nav mb-0">
                            <button type="button" class="tab-nav-btn active" onclick="switchAdminTab('pub_research')">Research Papers</button>
                            <button type="button" class="tab-nav-btn" onclick="switchAdminTab('pub_books')">Books</button>
                            <button type="button" class="tab-nav-btn" onclick="switchAdminTab('pub_conference')">Conference</button>
                        </div>

                        <!-- Research Papers -->
                        <div class="tab-panel active" id="panel-pub_research">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('pub_research')"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <div id="pub_research-container">
                                <?php
                                $prRows = !empty($pubResearch) ? $pubResearch : [['text'=>'']];
                                foreach ($prRows as $item):
                                ?>
                                <div class="dynamic-row">
                                    <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                    <input type="text" class="form-control form-control-sm" name="pub_research_text[]" value="<?php echo htmlspecialchars($item['text'] ?? ''); ?>" placeholder="Research paper details...">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Books -->
                        <div class="tab-panel" id="panel-pub_books">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('pub_books')"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <div id="pub_books-container">
                                <?php
                                $pbRows = !empty($pubBooks) ? $pubBooks : [['text'=>'']];
                                foreach ($pbRows as $item):
                                ?>
                                <div class="dynamic-row">
                                    <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                    <input type="text" class="form-control form-control-sm" name="pub_books_text[]" value="<?php echo htmlspecialchars($item['text'] ?? ''); ?>" placeholder="Book details...">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Conference -->
                        <div class="tab-panel" id="panel-pub_conference">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('pub_conference')"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <div id="pub_conference-container">
                                <?php
                                $pcRows = !empty($pubConference) ? $pubConference : [['text'=>'']];
                                foreach ($pcRows as $item):
                                ?>
                                <div class="dynamic-row">
                                    <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                    <input type="text" class="form-control form-control-sm" name="pub_conference_text[]" value="<?php echo htmlspecialchars($item['text'] ?? ''); ?>" placeholder="Conference details...">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ====== PROGRAMMES (OP/RC/STC/FDP) ====== -->
                <div class="section-box">
                    <div class="section-box-header"><span><i class="fas fa-certificate"></i> Programmes (OP / RC / STC / FDP)</span></div>
                    <div class="section-box-body">

                        <!-- Orientation -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-primary mb-0">Orientation Programme (OP)</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('prog_orientation')"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <div id="prog_orientation-container">
                                <?php
                                $poRows = !empty($progOrientation) ? $progOrientation : [['text'=>'']];
                                foreach ($poRows as $item):
                                ?>
                                <div class="dynamic-row">
                                    <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                    <input type="text" class="form-control form-control-sm" name="prog_orientation[]" value="<?php echo htmlspecialchars($item['text'] ?? ''); ?>" placeholder="e.g. Orientation Programme organized by UGC-ASC...">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Refresher -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-primary mb-0">Refresher Course (RC)</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('prog_refresher')"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <div id="prog_refresher-container">
                                <?php
                                $prfRows = !empty($progRefresher) ? $progRefresher : [['text'=>'']];
                                foreach ($prfRows as $item):
                                ?>
                                <div class="dynamic-row">
                                    <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                    <input type="text" class="form-control form-control-sm" name="prog_refresher[]" value="<?php echo htmlspecialchars($item['text'] ?? ''); ?>" placeholder="e.g. Refresher Course in Chemistry...">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- STC / FDP -->
                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-primary mb-0">Short Term Course / FDP</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('prog_stc_fdp')"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <div id="prog_stc_fdp-container">
                                <?php
                                $psRows = !empty($progStcFdp) ? $progStcFdp : [['text'=>'']];
                                foreach ($psRows as $item):
                                ?>
                                <div class="dynamic-row">
                                    <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                    <input type="text" class="form-control form-control-sm" name="prog_stc_fdp[]" value="<?php echo htmlspecialchars($item['text'] ?? ''); ?>" placeholder="e.g. Workshop on MOOCs...">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ====== SUBMIT ====== -->
                <div class="d-flex justify-content-between mb-5">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?> Principal
                    </button>
                    <a href="principal-list.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // ============================
    // PHOTO PREVIEW
    // ============================
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('photoPreview');
                container.innerHTML = '<img src="' + e.target.result + '" alt="Preview" id="photoImg">';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ============================
    // DYNAMIC ROW TEMPLATES
    // ============================
    const templates = {
        education: `
            <div class="dynamic-row">
                <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Degree</label>
                        <input type="text" class="form-control form-control-sm" name="edu_degree[]" placeholder="e.g. Ph.D, Chemistry">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small fw-bold">Board / University</label>
                        <input type="text" class="form-control form-control-sm" name="edu_board[]" placeholder="e.g. Gauhati University, Assam">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Year</label>
                        <input type="text" class="form-control form-control-sm" name="edu_year[]" placeholder="2013">
                    </div>
                </div>
            </div>`,

        book_pub: `
            <div class="dynamic-row">
                <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                <label class="form-label small fw-bold">Book Title / Details</label>
                <input type="text" class="form-control form-control-sm" name="book_pub_title[]" placeholder="e.g. Fundamentals of Spectroscopy, ISBN ..., Year: 2018, Publisher...">
            </div>`,

        book_editor: `
            <div class="dynamic-row">
                <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                <label class="form-label small fw-bold">Book Title / Details</label>
                <input type="text" class="form-control form-control-sm" name="book_editor_title[]" placeholder="e.g. Proceedings of National Seminar..., ISBN ...">
            </div>`,

        research: `
            <div class="dynamic-row">
                <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                <div class="row g-2">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Project Title</label>
                        <input type="text" class="form-control form-control-sm" name="res_title[]" placeholder="Project title...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Sponsored By</label>
                        <input type="text" class="form-control form-control-sm" name="res_sponsored[]" placeholder="UGC, New Delhi">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Period / Grant No.</label>
                        <input type="text" class="form-control form-control-sm" name="res_period[]" placeholder="F.5-292/2007...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Amount</label>
                        <input type="text" class="form-control form-control-sm" name="res_amount[]" placeholder="Rs. 80,000">
                    </div>
                </div>
            </div>`,

        pub_research: `
            <div class="dynamic-row">
                <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                <input type="text" class="form-control form-control-sm" name="pub_research_text[]" placeholder="Research paper details...">
            </div>`,

        pub_books: `
            <div class="dynamic-row">
                <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                <input type="text" class="form-control form-control-sm" name="pub_books_text[]" placeholder="Book details...">
            </div>`,

        pub_conference: `
            <div class="dynamic-row">
                <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                <input type="text" class="form-control form-control-sm" name="pub_conference_text[]" placeholder="Conference details...">
            </div>`,

        prog_orientation: `
            <div class="dynamic-row">
                <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                <input type="text" class="form-control form-control-sm" name="prog_orientation[]" placeholder="e.g. Orientation Programme organized by UGC-ASC...">
            </div>`,

        prog_refresher: `
            <div class="dynamic-row">
                <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                <input type="text" class="form-control form-control-sm" name="prog_refresher[]" placeholder="e.g. Refresher Course in Chemistry...">
            </div>`,

        prog_stc_fdp: `
            <div class="dynamic-row">
                <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                <input type="text" class="form-control form-control-sm" name="prog_stc_fdp[]" placeholder="e.g. Workshop on MOOCs...">
            </div>`
    };

    // ============================
    // ADD / REMOVE ROW
    // ============================
    function addRow(type) {
        const container = document.getElementById(type + '-container');
        if (container && templates[type]) {
            container.insertAdjacentHTML('beforeend', templates[type]);
        }
    }

    function removeRow(btn) {
        const row = btn.closest('.dynamic-row');
        const container = row.parentElement;
        // Keep at least one row
        if (container.querySelectorAll('.dynamic-row').length > 1) {
            row.remove();
        } else {
            // Clear inputs instead of removing
            row.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
        }
    }

    // ============================
    // PUBLICATION TABS
    // ============================
    function switchAdminTab(tabName) {
        document.querySelectorAll('.tab-nav-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

        const tabMap = { pub_research: 0, pub_books: 1, pub_conference: 2 };
        document.querySelectorAll('.tab-nav-btn')[tabMap[tabName]].classList.add('active');
        document.getElementById('panel-' + tabName).classList.add('active');
    }
    </script>
</body>
</html>
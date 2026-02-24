<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/IQAC.php';

$iqacModel = new IQAC();

// Get data for each category
$notices = $iqacModel->getDocumentsByCategory('notice');
$utility = $iqacModel->getDocumentsByCategory('utility');
$aqar = $iqacModel->getDocumentsByCategory('aqar');
$quest = $iqacModel->getDocumentsByCategory('quest');
$academic = $iqacModel->getDocumentsByCategory('academic');
$prospectus = $iqacModel->getDocumentsByCategory('prospectus');
$activity = $iqacModel->getDocumentsByCategory('activity');
$nirf = $iqacModel->getDocumentsByCategory('nirf');
$minutes = $iqacModel->getDocumentsByCategory('minutes');
$annual = $iqacModel->getDocumentsByCategory('annual');
$accreditation = $iqacModel->getDocumentsByCategory('accreditation');
$members = $iqacModel->getAllMembers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IQAC - Gyanpeeth Degree College, Nikashi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* [Keep all your existing CSS styles from the original file] */
        .page-banner {
            background: linear-gradient(rgba(0,0,0,0.58), rgba(0,0,0,0.58)),
                        url('assets/images/banner-bg.jpg') center/cover no-repeat;
            padding: 70px 0 45px;
            color: white;
            min-height: 180px;
            display: flex;
            align-items: center;
        }
        .page-banner h1 {
            font-size: 38px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0 0 10px;
        }
        .breadcrumb-item a  { color: #ffc107; text-decoration: none; font-size: 13px; }
        .breadcrumb-item.active { color: #ccc; font-size: 13px; }
        .breadcrumb-item + .breadcrumb-item::before { color: #aaa; content: "›"; }
        .iqac-page { padding: 45px 0 60px; background: #f4f6f9; }
        .iqac-tabs-wrapper {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        .iqac-tabs {
            display: flex;
            flex-wrap: wrap;
            border-bottom: 3px solid #e8ecf0;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .iqac-tabs li { margin: 0; }
        .iqac-tabs li a {
            display: block;
            padding: 14px 22px;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            text-decoration: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -3px;
            transition: all 0.25s;
            white-space: nowrap;
        }
        .iqac-tabs li a:hover { color: #1e3c72; background: #f0f4ff; }
        .iqac-tabs li a.active {
            color: white;
            background: #1e3c72;
            border-bottom-color: #1e3c72;
        }
        .tab-panel { display: none; padding: 28px 30px; }
        .tab-panel.active { display: block; }
        .iqac-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .iqac-table thead tr {
            background: #1e3c72;
            color: white;
        }
        .iqac-table th {
            padding: 13px 18px;
            font-weight: 600;
            text-align: left;
        }
        .iqac-table td {
            padding: 12px 18px;
            border-bottom: 1px solid #e8ecf0;
            color: #444;
            vertical-align: middle;
        }
        .iqac-table tr:hover td { background: #f8faff; }
        .iqac-table tr:last-child td { border-bottom: none; }
        .btn-view {
            background: #5a6a82;
            color: white;
            border: none;
            padding: 6px 18px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }
        .btn-view:hover { background: #1e3c72; color: white; }
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #999;
        }
        .empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }
        .composition-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .composition-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }
        .composition-card .role {
            display: inline-block;
            background: #1e3c72;
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .composition-card .name { font-size: 15px; font-weight: 700; color: #222; margin: 8px 0 4px; }
        .composition-card .designation { font-size: 12px; color: #777; }
        @media (max-width: 768px) {
            .page-banner h1 { font-size: 26px; }
            .iqac-tabs li a { padding: 10px 14px; font-size: 12px; }
            .tab-panel { padding: 18px 15px; }
            .iqac-table { font-size: 13px; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/components/header.php'; ?>
<?php include __DIR__ . '/components/navigation.php'; ?>

<!-- Page Banner -->
<div class="page-banner">
    <div class="container">
        <h1><i class="fas fa-award me-3"></i>IQAC</h1>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">IQAC</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="iqac-page">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="iqac-tabs-wrapper">

                    <!-- Tab Links -->
                    <ul class="iqac-tabs">
                        <li><a href="#" class="active" data-tab="notice">Notice</a></li>
                        <li><a href="#" data-tab="utility">Utility Format</a></li>
                        <li><a href="#" data-tab="aqar">AQAR</a></li>
                        <li><a href="#" data-tab="quest">Quest</a></li>
                        <li><a href="#" data-tab="academic">Academic Calendar</a></li>
                        <li><a href="#" data-tab="prospectus">Prospectus</a></li>
                        <li><a href="#" data-tab="activity">Activity</a></li>
                        <li><a href="#" data-tab="nirf">NIRF</a></li>
                        <li><a href="#" data-tab="minutes">Minutes</a></li>
                        <li><a href="#" data-tab="annual">Annual Reports</a></li>
                        <li><a href="#" data-tab="composition">IQAC Composition</a></li>
                        <li><a href="#" data-tab="accreditation">Accreditation &amp; Affiliation</a></li>
                    </ul>

                    <!-- NOTICE Tab -->
                    <div class="tab-panel active" id="tab-notice">
                        <?php if (count($notices) > 0): ?>
                        <table class="iqac-table">
                            <thead>
                                <tr>
                                    <th style="width:80px">Sl.no.</th>
                                    <th>Title</th>
                                    <th style="width:160px">Date &amp; Time</th>
                                    <th style="width:100px">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notices as $idx => $row): ?>
                                <tr>
                                    <td><?php echo $idx + 1; ?></td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo $row['date'] ? date('Y-m-d', strtotime($row['date'])) : '-'; ?></td>
                                    <td>
                                        <?php if ($row['file_path']): ?>
                                        <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank" class="btn-view">
                                            <i class="fas fa-eye me-1"></i>view
                                        </a>
                                        <?php else: ?>
                                        <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No notices available at this time.</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Repeat for other tabs with their respective data -->
                    <?php
                    $tabs = [
                        'utility' => $utility,
                        'aqar' => $aqar,
                        'quest' => $quest,
                        'academic' => $academic,
                        'prospectus' => $prospectus,
                        'activity' => $activity,
                        'nirf' => $nirf,
                        'minutes' => $minutes,
                        'annual' => $annual,
                        'accreditation' => $accreditation
                    ];
                    
                    foreach ($tabs as $tabName => $data):
                    ?>
                    <div class="tab-panel" id="tab-<?php echo $tabName; ?>">
                        <?php if (count($data) > 0): ?>
                        <table class="iqac-table">
                            <thead>
                                <tr>
                                    <th style="width:80px">Sl.no.</th>
                                    <th>Title</th>
                                    <th style="width:160px"><?php echo in_array($tabName, ['aqar', 'quest', 'academic', 'prospectus', 'annual', 'nirf']) ? 'Year' : 'Date'; ?></th>
                                    <th style="width:100px">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $idx => $row): ?>
                                <tr>
                                    <td><?php echo $idx + 1; ?></td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td>
                                        <?php 
                                        if ($row['year']) {
                                            echo htmlspecialchars($row['year']);
                                        } elseif ($row['date']) {
                                            echo date('Y-m-d', strtotime($row['date']));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($row['file_path']): ?>
                                        <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank" class="btn-view">
                                            <i class="fas fa-eye me-1"></i>view
                                        </a>
                                        <?php else: ?>
                                        <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No documents available.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>

                    <!-- IQAC COMPOSITION Tab -->
                    <div class="tab-panel" id="tab-composition">
                        <?php if (count($members) > 0): ?>
                        <h5 class="mb-4 fw-bold" style="color:#1e3c72; border-bottom:2px solid #1e3c72; padding-bottom:10px;">
                            IQAC Members
                        </h5>
                        <div class="row g-3">
                            <?php foreach ($members as $m): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="composition-card">
                                    <span class="role"><?php echo htmlspecialchars($m['role']); ?></span>
                                    <div class="name"><?php echo htmlspecialchars($m['name']); ?></div>
                                    <div class="designation"><?php echo htmlspecialchars($m['designation']); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>IQAC composition will be updated soon.</p>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.iqac-tabs a').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.iqac-tabs a').forEach(a => a.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});
</script>
</body>
</html>
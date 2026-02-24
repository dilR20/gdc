<?php
/**
 * WEBSITE FILE USAGE ANALYZER
 * Scans website to find used and unused files
 * 
 * Place in website root and run: http://localhost/website/analyze_files.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minutes

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>File Usage Analyzer</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1e1e1e;
            color: #e0e0e0;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: #2d2d2d;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        h1 {
            color: #4CAF50;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        h2 {
            color: #2196F3;
            margin-top: 30px;
            padding: 10px;
            background: #1a1a1a;
            border-left: 5px solid #2196F3;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-box {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #4CAF50;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #4CAF50;
        }
        .stat-label {
            color: #aaa;
            margin-top: 5px;
        }
        .file-list {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            max-height: 500px;
            overflow-y: auto;
            margin: 15px 0;
        }
        .file-item {
            padding: 8px 12px;
            margin: 5px 0;
            background: #2d2d2d;
            border-left: 3px solid #666;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        .file-item.used {
            border-left-color: #4CAF50;
            background: #1e3a1e;
        }
        .file-item.unused {
            border-left-color: #f44336;
            background: #3a1e1e;
        }
        .file-item.maybe {
            border-left-color: #FF9800;
            background: #3a2e1e;
        }
        .file-size {
            color: #aaa;
            font-size: 11px;
            margin-left: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 10px;
        }
        .badge.safe { background: #4CAF50; color: white; }
        .badge.delete { background: #f44336; color: white; }
        .badge.check { background: #FF9800; color: white; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        th {
            background: #1a1a1a;
            color: #4CAF50;
            font-weight: bold;
        }
        tr:hover {
            background: #333;
        }
        .action-btn {
            padding: 8px 15px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .action-btn:hover {
            background: #1976D2;
        }
        .action-btn.danger {
            background: #f44336;
        }
        .action-btn.danger:hover {
            background: #d32f2f;
        }
        pre {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
        }
        .warning {
            background: #3a2e1e;
            border: 2px solid #FF9800;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .success {
            background: #1e3a1e;
            border: 2px solid #4CAF50;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Website File Usage Analyzer</h1>
        <p style="color: #aaa;">Analyzing: <?php echo __DIR__; ?></p>
        
        <?php
        // ==========================================
        // CONFIGURATION
        // ==========================================
        
        $excludeDirs = [
            '.git',
            'node_modules',
            'vendor',
            '.vscode',
            '.idea',
        ];
        
        $excludeFiles = [
            '.gitignore',
            '.htaccess',
            'composer.json',
            'composer.lock',
            'package.json',
            'package-lock.json',
        ];
        
        // ==========================================
        // SCAN FUNCTIONS
        // ==========================================
        
        function getAllFiles($dir, $excludeDirs = []) {
            $files = [];
            $items = scandir($dir);
            
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $path = $dir . '/' . $item;
                $relativePath = str_replace(__DIR__ . '/', '', $path);
                
                if (is_dir($path)) {
                    if (in_array($item, $excludeDirs)) continue;
                    $files = array_merge($files, getAllFiles($path, $excludeDirs));
                } else {
                    $files[] = $relativePath;
                }
            }
            
            return $files;
        }
        
        function findReferences($content) {
            $references = [];
            
            // Find require/include statements
            preg_match_all('/(?:require|include)(?:_once)?\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
            if (!empty($matches[1])) {
                $references = array_merge($references, $matches[1]);
            }
            
            // Find file_get_contents, fopen, etc.
            preg_match_all('/(?:file_get_contents|fopen|readfile|file_exists|is_file)\s*\([\'"]([^\'"]+)[\'"]/', $content, $matches);
            if (!empty($matches[1])) {
                $references = array_merge($references, $matches[1]);
            }
            
            // Find image sources
            preg_match_all('/src\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
            if (!empty($matches[1])) {
                $references = array_merge($references, $matches[1]);
            }
            
            // Find href links (CSS, JS)
            preg_match_all('/href\s*=\s*[\'"]([^\'"]+\.(?:css|js))[\'"]/', $content, $matches);
            if (!empty($matches[1])) {
                $references = array_merge($references, $matches[1]);
            }
            
            // Find action/redirect locations
            preg_match_all('/(?:action|Location:|header\([\'"]Location:)\s*[\'"]?([^\'"?\s]+)/', $content, $matches);
            if (!empty($matches[1])) {
                $references = array_merge($references, $matches[1]);
            }
            
            return array_unique($references);
        }
        
        function normalizeFile($file) {
            // Remove leading slashes and dots
            $file = ltrim($file, './');
            $file = str_replace('../', '', $file);
            $file = str_replace('./', '', $file);
            return $file;
        }
        
        function formatFileSize($bytes) {
            if ($bytes >= 1048576) {
                return number_format($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes >= 1024) {
                return number_format($bytes / 1024, 2) . ' KB';
            } else {
                return $bytes . ' bytes';
            }
        }
        
        // ==========================================
        // ANALYZE FILES
        // ==========================================
        
        echo "<h2>🔍 Scanning Files...</h2>";
        
        $allFiles = getAllFiles(__DIR__, $excludeDirs);
        $phpFiles = array_filter($allFiles, function($f) { return pathinfo($f, PATHINFO_EXTENSION) === 'php'; });
        $otherFiles = array_diff($allFiles, $phpFiles);
        
        echo "<div class='stats'>";
        echo "<div class='stat-box'>";
        echo "<div class='stat-number'>" . count($allFiles) . "</div>";
        echo "<div class='stat-label'>Total Files</div>";
        echo "</div>";
        
        echo "<div class='stat-box'>";
        echo "<div class='stat-number'>" . count($phpFiles) . "</div>";
        echo "<div class='stat-label'>PHP Files</div>";
        echo "</div>";
        
        echo "<div class='stat-box'>";
        echo "<div class='stat-number'>" . count($otherFiles) . "</div>";
        echo "<div class='stat-label'>Other Files</div>";
        echo "</div>";
        echo "</div>";
        
        // Find all references
        echo "<h2>📝 Finding References...</h2>";
        
        $allReferences = [];
        $fileReferences = []; // Track which files reference which
        
        foreach ($phpFiles as $file) {
            $content = file_get_contents(__DIR__ . '/' . $file);
            $refs = findReferences($content);
            
            foreach ($refs as $ref) {
                $normalized = normalizeFile($ref);
                $allReferences[] = $normalized;
                
                if (!isset($fileReferences[$normalized])) {
                    $fileReferences[$normalized] = [];
                }
                $fileReferences[$normalized][] = $file;
            }
        }
        
        $allReferences = array_unique($allReferences);
        
        echo "<p>Found " . count($allReferences) . " unique file references in PHP files.</p>";
        
        // Categorize files
        $usedFiles = [];
        $unusedFiles = [];
        $maybeUnused = [];
        
        // Entry points (always used)
        $entryPoints = [
            'index.php',
            'diagnostic.php',
            'debug_department.php',
            'analyze_files.php',
            'department.php',
            'faculty-profile.php',
        ];
        
        foreach ($allFiles as $file) {
            if (in_array(basename($file), $excludeFiles)) continue;
            
            $isUsed = false;
            
            // Check if it's an entry point
            if (in_array(basename($file), $entryPoints)) {
                $usedFiles[] = $file;
                continue;
            }
            
            // Check if referenced
            foreach ($allReferences as $ref) {
                if (strpos($file, $ref) !== false || strpos($ref, $file) !== false) {
                    $isUsed = true;
                    break;
                }
            }
            
            if ($isUsed) {
                $usedFiles[] = $file;
            } else {
                // Special cases that might be used
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $basename = basename($file);
                
                // Config, database, common includes usually used
                if (in_array($basename, ['config.php', 'Database.php', 'Auth.php']) || 
                    strpos($file, 'config/') === 0 ||
                    strpos($file, 'includes/') === 0) {
                    $maybeUnused[] = $file;
                } 
                // Admin files might be accessed directly
                elseif (strpos($file, 'admin/') === 0 && $ext === 'php') {
                    $maybeUnused[] = $file;
                }
                // Assets might be used
                elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'css', 'js'])) {
                    $maybeUnused[] = $file;
                }
                else {
                    $unusedFiles[] = $file;
                }
            }
        }
        
        // ==========================================
        // DISPLAY RESULTS
        // ==========================================
        
        echo "<h2>📊 Summary</h2>";
        echo "<div class='stats'>";
        echo "<div class='stat-box' style='border-color: #4CAF50;'>";
        echo "<div class='stat-number'>" . count($usedFiles) . "</div>";
        echo "<div class='stat-label'>Used Files</div>";
        echo "</div>";
        
        echo "<div class='stat-box' style='border-color: #FF9800;'>";
        echo "<div class='stat-number'>" . count($maybeUnused) . "</div>";
        echo "<div class='stat-label'>Maybe Unused</div>";
        echo "</div>";
        
        echo "<div class='stat-box' style='border-color: #f44336;'>";
        echo "<div class='stat-number'>" . count($unusedFiles) . "</div>";
        echo "<div class='stat-label'>Likely Unused</div>";
        echo "</div>";
        echo "</div>";
        
        // UNUSED FILES
        if (!empty($unusedFiles)) {
            echo "<h2>❌ Likely Unused Files (" . count($unusedFiles) . ")</h2>";
            echo "<div class='warning'>";
            echo "<strong>⚠️ Warning:</strong> These files appear to be unused. Review carefully before deleting!";
            echo "</div>";
            
            $totalSize = 0;
            
            echo "<table>";
            echo "<tr><th>File</th><th>Size</th><th>Type</th><th>Action</th></tr>";
            
            foreach ($unusedFiles as $file) {
                $fullPath = __DIR__ . '/' . $file;
                $size = file_exists($fullPath) ? filesize($fullPath) : 0;
                $totalSize += $size;
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                
                echo "<tr>";
                echo "<td style='font-family: monospace;'>" . htmlspecialchars($file) . "</td>";
                echo "<td>" . formatFileSize($size) . "</td>";
                echo "<td>" . strtoupper($ext) . "</td>";
                echo "<td><span class='badge delete'>CAN DELETE</span></td>";
                echo "</tr>";
            }
            
            echo "</table>";
            echo "<p><strong>Total size of unused files:</strong> " . formatFileSize($totalSize) . "</p>";
        }
        
        // MAYBE UNUSED
        if (!empty($maybeUnused)) {
            echo "<h2>⚠️ Maybe Unused Files (" . count($maybeUnused) . ")</h2>";
            echo "<div class='warning'>";
            echo "<strong>Review Required:</strong> These files might be used but weren't detected. Check manually!";
            echo "</div>";
            
            echo "<table>";
            echo "<tr><th>File</th><th>Size</th><th>Reason</th></tr>";
            
            foreach ($maybeUnused as $file) {
                $fullPath = __DIR__ . '/' . $file;
                $size = file_exists($fullPath) ? filesize($fullPath) : 0;
                
                $reason = '';
                if (strpos($file, 'admin/') === 0) $reason = 'Admin file';
                elseif (strpos($file, 'includes/') === 0) $reason = 'Include file';
                elseif (strpos($file, 'config/') === 0) $reason = 'Config file';
                else $reason = 'Asset file';
                
                echo "<tr>";
                echo "<td style='font-family: monospace;'>" . htmlspecialchars($file) . "</td>";
                echo "<td>" . formatFileSize($size) . "</td>";
                echo "<td><span class='badge check'>$reason</span></td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
        
        // USED FILES
        echo "<h2>✅ Used Files (" . count($usedFiles) . ")</h2>";
        echo "<div class='success'>";
        echo "<strong>Safe:</strong> These files are actively used. Do NOT delete!";
        echo "</div>";
        
        echo "<div class='file-list'>";
        foreach ($usedFiles as $file) {
            $fullPath = __DIR__ . '/' . $file;
            $size = file_exists($fullPath) ? filesize($fullPath) : 0;
            
            echo "<div class='file-item used'>";
            echo htmlspecialchars($file);
            echo "<span class='file-size'>" . formatFileSize($size) . "</span>";
            echo "<span class='badge safe'>KEEP</span>";
            echo "</div>";
        }
        echo "</div>";
        
        // DUPLICATE FILES
        echo "<h2>🔄 Checking for Duplicates...</h2>";
        
        $duplicates = [];
        $fileHashes = [];
        
        foreach ($allFiles as $file) {
            $fullPath = __DIR__ . '/' . $file;
            if (!file_exists($fullPath)) continue;
            
            $hash = md5_file($fullPath);
            
            if (isset($fileHashes[$hash])) {
                if (!isset($duplicates[$hash])) {
                    $duplicates[$hash] = [$fileHashes[$hash]];
                }
                $duplicates[$hash][] = $file;
            } else {
                $fileHashes[$hash] = $file;
            }
        }
        
        if (!empty($duplicates)) {
            echo "<div class='warning'>";
            echo "<strong>Found " . count($duplicates) . " sets of duplicate files!</strong>";
            echo "</div>";
            
            foreach ($duplicates as $hash => $files) {
                echo "<div class='file-list'>";
                echo "<strong>Duplicate Set (MD5: " . substr($hash, 0, 8) . "...):</strong>";
                foreach ($files as $file) {
                    $fullPath = __DIR__ . '/' . $file;
                    $size = filesize($fullPath);
                    echo "<div class='file-item maybe'>";
                    echo htmlspecialchars($file);
                    echo "<span class='file-size'>" . formatFileSize($size) . "</span>";
                    echo "</div>";
                }
                echo "</div>";
            }
        } else {
            echo "<p style='color: #4CAF50;'>✓ No duplicate files found!</p>";
        }
        
        // RECOMMENDATIONS
        echo "<h2>💡 Recommendations</h2>";
        
        echo "<div class='file-list'>";
        
        if (count($unusedFiles) > 0) {
            echo "<div class='file-item unused'>";
            echo "<strong>Delete " . count($unusedFiles) . " unused files</strong> to save " . formatFileSize(array_sum(array_map(function($f) {
                return file_exists(__DIR__ . '/' . $f) ? filesize(__DIR__ . '/' . $f) : 0;
            }, $unusedFiles))) . " of disk space";
            echo "</div>";
        }
        
        if (count($duplicates) > 0) {
            echo "<div class='file-item maybe'>";
            echo "<strong>Review and remove duplicate files</strong> - Keep one copy, delete others";
            echo "</div>";
        }
        
        if (count($maybeUnused) > 20) {
            echo "<div class='file-item maybe'>";
            echo "<strong>Review " . count($maybeUnused) . " 'maybe unused' files</strong> - Many might be deletable";
            echo "</div>";
        }
        
        echo "</div>";
        
        // EXPORT OPTIONS
        echo "<h2>📥 Export Results</h2>";
        
        echo "<div style='margin: 20px 0;'>";
        echo "<button class='action-btn' onclick='exportList(\"unused\")'>Export Unused Files List</button>";
        echo "<button class='action-btn' onclick='exportList(\"all\")'>Export Full Analysis</button>";
        echo "</div>";
        
        // Hidden data for export
        echo "<textarea id='unused-list' style='display:none;'>";
        foreach ($unusedFiles as $file) {
            echo $file . "\n";
        }
        echo "</textarea>";
        
        echo "<textarea id='all-analysis' style='display:none;'>";
        echo "FILE USAGE ANALYSIS - " . date('Y-m-d H:i:s') . "\n";
        echo "===========================================\n\n";
        echo "STATISTICS:\n";
        echo "Total Files: " . count($allFiles) . "\n";
        echo "Used Files: " . count($usedFiles) . "\n";
        echo "Maybe Unused: " . count($maybeUnused) . "\n";
        echo "Likely Unused: " . count($unusedFiles) . "\n";
        echo "\n===========================================\n";
        echo "\nUNUSED FILES:\n";
        foreach ($unusedFiles as $file) echo "  - " . $file . "\n";
        echo "\n===========================================\n";
        echo "\nMAYBE UNUSED FILES:\n";
        foreach ($maybeUnused as $file) echo "  - " . $file . "\n";
        echo "</textarea>";
        
        ?>
        
        <script>
        function exportList(type) {
            const text = document.getElementById(type + '-list') || document.getElementById(type + '-analysis');
            const blob = new Blob([text.value], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = type + '-files-' + new Date().toISOString().split('T')[0] + '.txt';
            a.click();
            window.URL.revokeObjectURL(url);
        }
        </script>
        
        <hr style="margin: 40px 0; border-color: #444;">
        <p style="text-align: center; color: #aaa;">
            <strong>⚠️ IMPORTANT:</strong> Always backup before deleting files!<br>
            <small>Generated: <?php echo date('Y-m-d H:i:s'); ?></small>
        </p>
    </div>
</body>
</html>
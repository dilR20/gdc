<?php
// Check current database table structure
require_once '../config/config.php';
require_once '../includes/Database.php';

$database = new Database();
$conn = $database->getConnection();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Structure Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #1e3c72; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
        .missing { background: #fff3cd; font-weight: bold; }
        .exists { background: #d4edda; }
        .sql-box { background: #f8f9fa; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0; font-family: monospace; white-space: pre-wrap; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Database Structure Check</h1>
        
        <?php
        // Required columns for departments table
        $requiredDeptColumns = [
            'id' => 'int(11)',
            'name' => 'varchar(200)',
            'code' => 'varchar(20)',
            'description' => 'text',
            'head_of_department' => 'varchar(200)',
            'established_year' => 'int(4)',
            'is_active' => 'tinyint(1)',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp'
        ];
        
        // Required columns for courses table
        $requiredCourseColumns = [
            'id' => 'int(11)',
            'department_id' => 'int(11)',
            'course_name' => 'varchar(200)',
            'course_code' => 'varchar(50)',
            'semester' => 'varchar(50)',
            'seat_capacity' => 'int(11)',
            'description' => 'text',
            'is_active' => 'tinyint(1)',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp'
        ];
        
        // Check departments table
        echo "<h2>Departments Table</h2>";
        
        try {
            $stmt = $conn->query("DESCRIBE departments");
            $existingColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $columnNames = array_column($existingColumns, 'Field');
            
            echo "<table>";
            echo "<tr><th>Column Name</th><th>Status</th><th>Current Type</th></tr>";
            
            $missingDept = [];
            foreach ($requiredDeptColumns as $colName => $colType) {
                $exists = in_array($colName, $columnNames);
                $class = $exists ? 'exists' : 'missing';
                
                echo "<tr class='$class'>";
                echo "<td><strong>$colName</strong></td>";
                echo "<td>" . ($exists ? "✅ EXISTS" : "❌ MISSING") . "</td>";
                
                if ($exists) {
                    $currentCol = array_filter($existingColumns, fn($c) => $c['Field'] == $colName);
                    $currentCol = reset($currentCol);
                    echo "<td>" . $currentCol['Type'] . "</td>";
                } else {
                    echo "<td>$colType (required)</td>";
                    $missingDept[] = $colName;
                }
                echo "</tr>";
            }
            echo "</table>";
            
            if (!empty($missingDept)) {
                echo "<div style='background: #fff3cd; padding: 15px; margin: 20px 0; border-left: 4px solid #ffc107;'>";
                echo "<strong>⚠️ Missing columns in departments table:</strong> " . implode(', ', $missingDept);
                echo "</div>";
            } else {
                echo "<div style='background: #d4edda; padding: 15px; margin: 20px 0; border-left: 4px solid #28a745;'>";
                echo "<strong>✅ All required columns exist in departments table!</strong>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error checking departments table: " . $e->getMessage() . "</p>";
        }
        
        // Check courses table
        echo "<h2>Courses Table</h2>";
        
        try {
            $stmt = $conn->query("DESCRIBE courses");
            $existingColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $columnNames = array_column($existingColumns, 'Field');
            
            echo "<table>";
            echo "<tr><th>Column Name</th><th>Status</th><th>Current Type</th></tr>";
            
            $missingCourse = [];
            foreach ($requiredCourseColumns as $colName => $colType) {
                $exists = in_array($colName, $columnNames);
                $class = $exists ? 'exists' : 'missing';
                
                echo "<tr class='$class'>";
                echo "<td><strong>$colName</strong></td>";
                echo "<td>" . ($exists ? "✅ EXISTS" : "❌ MISSING") . "</td>";
                
                if ($exists) {
                    $currentCol = array_filter($existingColumns, fn($c) => $c['Field'] == $colName);
                    $currentCol = reset($currentCol);
                    echo "<td>" . $currentCol['Type'] . "</td>";
                } else {
                    echo "<td>$colType (required)</td>";
                    $missingCourse[] = $colName;
                }
                echo "</tr>";
            }
            echo "</table>";
            
            if (!empty($missingCourse)) {
                echo "<div style='background: #fff3cd; padding: 15px; margin: 20px 0; border-left: 4px solid #ffc107;'>";
                echo "<strong>⚠️ Missing columns in courses table:</strong> " . implode(', ', $missingCourse);
                echo "</div>";
            } else {
                echo "<div style='background: #d4edda; padding: 15px; margin: 20px 0; border-left: 4px solid #28a745;'>";
                echo "<strong>✅ All required columns exist in courses table!</strong>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error checking courses table: " . $e->getMessage() . "</p>";
        }
        
        // Generate SQL to add missing columns
        if (!empty($missingDept) || !empty($missingCourse)) {
            echo "<h2>🔧 SQL to Fix Missing Columns</h2>";
            echo "<p>Copy and run this SQL in phpMyAdmin:</p>";
            echo "<div class='sql-box'>";
            
            if (!empty($missingDept)) {
                echo "-- Add missing columns to departments table\n";
                foreach ($missingDept as $col) {
                    if ($col == 'code') {
                        echo "ALTER TABLE `departments` ADD COLUMN `code` varchar(20) DEFAULT NULL AFTER `name`;\n";
                    } elseif ($col == 'description') {
                        echo "ALTER TABLE `departments` ADD COLUMN `description` text AFTER `code`;\n";
                    } elseif ($col == 'head_of_department') {
                        echo "ALTER TABLE `departments` ADD COLUMN `head_of_department` varchar(200) DEFAULT NULL AFTER `description`;\n";
                    } elseif ($col == 'established_year') {
                        echo "ALTER TABLE `departments` ADD COLUMN `established_year` int(4) DEFAULT NULL AFTER `head_of_department`;\n";
                    } elseif ($col == 'is_active') {
                        echo "ALTER TABLE `departments` ADD COLUMN `is_active` tinyint(1) DEFAULT 1 AFTER `established_year`;\n";
                    } elseif ($col == 'created_at') {
                        echo "ALTER TABLE `departments` ADD COLUMN `created_at` timestamp DEFAULT CURRENT_TIMESTAMP AFTER `is_active`;\n";
                    } elseif ($col == 'updated_at') {
                        echo "ALTER TABLE `departments` ADD COLUMN `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;\n";
                    }
                }
                echo "\n";
            }
            
            if (!empty($missingCourse)) {
                echo "-- Add missing columns to courses table\n";
                foreach ($missingCourse as $col) {
                    if ($col == 'course_code') {
                        echo "ALTER TABLE `courses` ADD COLUMN `course_code` varchar(50) DEFAULT NULL AFTER `course_name`;\n";
                    } elseif ($col == 'semester') {
                        echo "ALTER TABLE `courses` ADD COLUMN `semester` varchar(50) DEFAULT NULL AFTER `course_code`;\n";
                    } elseif ($col == 'seat_capacity') {
                        echo "ALTER TABLE `courses` ADD COLUMN `seat_capacity` int(11) DEFAULT NULL AFTER `semester`;\n";
                    } elseif ($col == 'description') {
                        echo "ALTER TABLE `courses` ADD COLUMN `description` text AFTER `seat_capacity`;\n";
                    } elseif ($col == 'is_active') {
                        echo "ALTER TABLE `courses` ADD COLUMN `is_active` tinyint(1) DEFAULT 1 AFTER `description`;\n";
                    } elseif ($col == 'created_at') {
                        echo "ALTER TABLE `courses` ADD COLUMN `created_at` timestamp DEFAULT CURRENT_TIMESTAMP AFTER `is_active`;\n";
                    } elseif ($col == 'updated_at') {
                        echo "ALTER TABLE `courses` ADD COLUMN `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;\n";
                    }
                }
            }
            
            echo "</div>";
            
            echo "<p><strong>After running the SQL, refresh this page to verify all columns are added.</strong></p>";
        }
        ?>
        
        <hr style="margin: 30px 0;">
        <a href="index.php" class="btn">← Back to Dashboard</a>
        <a href="?" class="btn">🔄 Refresh Check</a>
    </div>
</body>
</html>
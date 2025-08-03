<?php
// Setup script for Student Enrollment System
// This script helps verify the system configuration

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Student Enrollment System - Setup</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: #f8f9fa; }
        .setup-card { 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
            margin: 20px auto; 
            max-width: 800px; 
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='setup-card p-4'>
            <h2 class='text-center mb-4'>
                <i class='fas fa-graduation-cap'></i> Student Enrollment System Setup
            </h2>";

// Check PHP version
echo "<div class='alert alert-info'>
        <h5><i class='fas fa-info-circle'></i> System Requirements Check</h5>";

$php_version = phpversion();
echo "<p><strong>PHP Version:</strong> $php_version</p>";

if (version_compare($php_version, '7.4.0', '>=')) {
    echo "<p class='text-success'><i class='fas fa-check'></i> PHP version is compatible</p>";
} else {
    echo "<p class='text-danger'><i class='fas fa-times'></i> PHP version must be 7.4 or higher</p>";
}

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'session'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (empty($missing_extensions)) {
    echo "<p class='text-success'><i class='fas fa-check'></i> All required PHP extensions are installed</p>";
} else {
    echo "<p class='text-danger'><i class='fas fa-times'></i> Missing extensions: " . implode(', ', $missing_extensions) . "</p>";
}

echo "</div>";

// Test database connection
echo "<div class='alert alert-info'>
        <h5><i class='fas fa-database'></i> Database Connection Test</h5>";

try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "<p class='text-success'><i class='fas fa-check'></i> Database connection successful</p>";
        
        // Check if tables exist
        $tables = ['users', 'students', 'programs', 'class_schedules', 'announcements'];
        $existing_tables = [];
        
        foreach ($tables as $table) {
            $query = "SHOW TABLES LIKE '$table'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            if ($stmt->fetch()) {
                $existing_tables[] = $table;
            }
        }
        
        if (count($existing_tables) == count($tables)) {
            echo "<p class='text-success'><i class='fas fa-check'></i> All database tables exist</p>";
        } else {
            echo "<p class='text-warning'><i class='fas fa-exclamation-triangle'></i> Some tables are missing. Please import database.sql</p>";
        }
        
    } else {
        echo "<p class='text-danger'><i class='fas fa-times'></i> Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='text-danger'><i class='fas fa-times'></i> Database connection error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Installation instructions
echo "<div class='alert alert-warning'>
        <h5><i class='fas fa-exclamation-triangle'></i> Installation Instructions</h5>
        <ol>
            <li>Make sure XAMPP is running (Apache and MySQL)</li>
            <li>Import the database.sql file into phpMyAdmin</li>
            <li>Verify database connection settings in config/database.php</li>
            <li>Access the system at: <a href='index.php'>index.php</a></li>
            <li>Login with admin credentials: admin / password</li>
        </ol>
    </div>";

// File permissions check
echo "<div class='alert alert-info'>
        <h5><i class='fas fa-file'></i> File Structure Check</h5>";

$required_files = [
    'config/database.php',
    'index.php',
    'register.php',
    'dashboard.php',
    'logout.php',
    'admin/students.php',
    'admin/announcements.php',
    'admin/schedules.php',
    'admin/reports.php'
];

$missing_files = [];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        $missing_files[] = $file;
    }
}

if (empty($missing_files)) {
    echo "<p class='text-success'><i class='fas fa-check'></i> All required files are present</p>";
} else {
    echo "<p class='text-danger'><i class='fas fa-times'></i> Missing files: " . implode(', ', $missing_files) . "</p>";
}

echo "</div>";

// Quick links
echo "<div class='text-center mt-4'>
        <a href='index.php' class='btn btn-primary me-2'>
            <i class='fas fa-sign-in-alt'></i> Go to Login
        </a>
        <a href='register.php' class='btn btn-success me-2'>
            <i class='fas fa-user-plus'></i> Student Registration
        </a>
        <a href='README.md' class='btn btn-info'>
            <i class='fas fa-book'></i> View Documentation
        </a>
    </div>";

echo "</div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
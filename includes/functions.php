<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: dashboard.php');
        exit();
    }
}

// Login user
function loginUser($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

// Register student
function registerStudent($data) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Generate student ID
        $stmt = $pdo->query("SELECT COUNT(*) FROM students");
        $count = $stmt->fetchColumn();
        $studentId = 'STU' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);
        
        // Create user account
        $hashedPassword = password_hash($data['email'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'student')");
        $stmt->execute([$data['email'], $data['email'], $hashedPassword]);
        $userId = $pdo->lastInsertId();
        
        // Create student record
        $stmt = $pdo->prepare("
            INSERT INTO students (user_id, student_id, first_name, last_name, date_of_birth, gender, email, phone, grade_level, program) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $studentId,
            $data['first_name'],
            $data['last_name'],
            $data['date_of_birth'],
            $data['gender'],
            $data['email'],
            $data['phone'],
            $data['grade_level'],
            $data['program']
        ]);
        
        $pdo->commit();
        return ['success' => true, 'student_id' => $studentId];
    } catch (Exception $e) {
        $pdo->rollback();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Get dashboard statistics
function getDashboardStats() {
    global $pdo;
    
    $stats = [];
    
    // Enrolled students count
    $stmt = $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'enrolled'");
    $stats['enrolled'] = $stmt->fetchColumn();
    
    // Pending applicants count
    $stmt = $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'pending'");
    $stats['pending'] = $stmt->fetchColumn();
    
    // Male students count
    $stmt = $pdo->query("SELECT COUNT(*) FROM students WHERE gender = 'Male'");
    $stats['male'] = $stmt->fetchColumn();
    
    // Female students count
    $stmt = $pdo->query("SELECT COUNT(*) FROM students WHERE gender = 'Female'");
    $stats['female'] = $stmt->fetchColumn();
    
    return $stats;
}

// Get student schedule
function getStudentSchedule($gradeLevel) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT s.subject_name, cs.time_start, cs.time_end, cs.days, cs.room, cs.teacher
        FROM class_schedules cs
        JOIN subjects s ON cs.subject_id = s.id
        WHERE cs.grade_level = ?
        ORDER BY cs.time_start
    ");
    $stmt->execute([$gradeLevel]);
    return $stmt->fetchAll();
}

// Get recent announcements
function getRecentAnnouncements($limit = 5) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT a.title, a.content, a.created_at, u.username
        FROM announcements a
        JOIN users u ON a.created_by = u.id
        ORDER BY a.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

// Get all students
function getAllStudents() {
    global $pdo;
    
    $stmt = $pdo->query("
        SELECT * FROM students
        ORDER BY created_at DESC
    ");
    return $stmt->fetchAll();
}

// Update student status
function updateStudentStatus($studentId, $status) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE students SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $studentId]);
}

// Add announcement
function addAnnouncement($title, $content, $userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO announcements (title, content, created_by) VALUES (?, ?, ?)");
    return $stmt->execute([$title, $content, $userId]);
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format time
function formatTime($time) {
    return date('g:i A', strtotime($time));
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
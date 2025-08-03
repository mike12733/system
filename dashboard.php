<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get dashboard statistics
$stats = [];

// Enrolled students count
$enrolled_query = "SELECT COUNT(*) as count FROM students WHERE enrollment_status = 'enrolled'";
$enrolled_stmt = $db->prepare($enrolled_query);
$enrolled_stmt->execute();
$stats['enrolled'] = $enrolled_stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Pending applicants count
$pending_query = "SELECT COUNT(*) as count FROM students WHERE enrollment_status = 'pending'";
$pending_stmt = $db->prepare($pending_query);
$pending_stmt->execute();
$stats['pending'] = $pending_stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Male students count
$male_query = "SELECT COUNT(*) as count FROM students WHERE gender = 'male' AND enrollment_status = 'enrolled'";
$male_stmt = $db->prepare($male_query);
$male_stmt->execute();
$stats['male'] = $male_stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Female students count
$female_query = "SELECT COUNT(*) as count FROM students WHERE gender = 'female' AND enrollment_status = 'enrolled'";
$female_stmt = $db->prepare($female_query);
$female_stmt->execute();
$stats['female'] = $female_stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Get class schedule
$schedule_query = "SELECT * FROM class_schedules ORDER BY day_of_week, start_time";
$schedule_stmt = $db->prepare($schedule_query);
$schedule_stmt->execute();
$schedules = $schedule_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent announcements
$announcements_query = "SELECT a.*, u.username as author FROM announcements a 
                       JOIN users u ON a.author_id = u.id 
                       ORDER BY a.created_at DESC LIMIT 5";
$announcements_stmt = $db->prepare($announcements_query);
$announcements_stmt->execute();
$announcements = $announcements_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stats-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .schedule-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .announcements-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .announcement-item {
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
            border-radius: 0 10px 10px 0;
        }
        .day-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i> Student Enrollment System
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Dashboard Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-white" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <div class="card-body text-center">
                        <i class="fas fa-users stats-icon"></i>
                        <h3 class="mt-2"><?php echo $stats['enrolled']; ?></h3>
                        <p class="mb-0">Enrolled Students</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-white" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                    <div class="card-body text-center">
                        <i class="fas fa-clock stats-icon"></i>
                        <h3 class="mt-2"><?php echo $stats['pending']; ?></h3>
                        <p class="mb-0">Pending Applicants</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-white" style="background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);">
                    <div class="card-body text-center">
                        <i class="fas fa-male stats-icon"></i>
                        <h3 class="mt-2"><?php echo $stats['male']; ?></h3>
                        <p class="mb-0">Male Students</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-white" style="background: linear-gradient(135deg, #e83e8c 0%, #dc3545 100%);">
                    <div class="card-body text-center">
                        <i class="fas fa-female stats-icon"></i>
                        <h3 class="mt-2"><?php echo $stats['female']; ?></h3>
                        <p class="mb-0">Female Students</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Class Schedule -->
            <div class="col-lg-8 mb-4">
                <div class="schedule-table p-4">
                    <h4 class="mb-4">
                        <i class="fas fa-calendar-alt"></i> Class Schedule
                    </h4>
                    
                    <?php if (empty($schedules)): ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                            <p>No class schedules available</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Subject</th>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>Room</th>
                                        <th>Grade Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schedules as $schedule): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($schedule['subject']); ?></strong></td>
                                            <td>
                                                <span class="day-badge">
                                                    <?php echo htmlspecialchars($schedule['day_of_week']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                echo date('h:i A', strtotime($schedule['start_time'])) . ' - ' . 
                                                     date('h:i A', strtotime($schedule['end_time'])); 
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($schedule['room']); ?></td>
                                            <td><?php echo htmlspecialchars($schedule['grade_level']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Announcements -->
            <div class="col-lg-4 mb-4">
                <div class="announcements-card p-4">
                    <h4 class="mb-4">
                        <i class="fas fa-bullhorn"></i> Recent Announcements
                    </h4>
                    
                    <?php if (empty($announcements)): ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-bullhorn fa-2x mb-2"></i>
                            <p>No announcements available</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <div class="announcement-item">
                                <h6 class="mb-2"><?php echo htmlspecialchars($announcement['title']); ?></h6>
                                <p class="mb-2 text-muted small">
                                    <?php echo htmlspecialchars($announcement['content']); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($announcement['author']); ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo date('M d, Y', strtotime($announcement['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cogs"></i> Admin Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <a href="admin/students.php" class="btn btn-primary w-100">
                                        <i class="fas fa-users"></i> Manage Students
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="admin/announcements.php" class="btn btn-success w-100">
                                        <i class="fas fa-bullhorn"></i> Manage Announcements
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="admin/schedules.php" class="btn btn-info w-100">
                                        <i class="fas fa-calendar"></i> Manage Schedules
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="admin/reports.php" class="btn btn-warning w-100">
                                        <i class="fas fa-chart-bar"></i> View Reports
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
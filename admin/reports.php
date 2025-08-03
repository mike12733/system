<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get enrollment statistics
$stats = [];

// Total students
$total_query = "SELECT COUNT(*) as count FROM students";
$total_stmt = $db->prepare($total_query);
$total_stmt->execute();
$stats['total'] = $total_stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Enrolled students
$enrolled_query = "SELECT COUNT(*) as count FROM students WHERE enrollment_status = 'enrolled'";
$enrolled_stmt = $db->prepare($enrolled_query);
$enrolled_stmt->execute();
$stats['enrolled'] = $enrolled_stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Pending students
$pending_query = "SELECT COUNT(*) as count FROM students WHERE enrollment_status = 'pending'";
$pending_stmt = $db->prepare($pending_query);
$pending_stmt->execute();
$stats['pending'] = $pending_stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Rejected students
$rejected_query = "SELECT COUNT(*) as count FROM students WHERE enrollment_status = 'rejected'";
$rejected_stmt = $db->prepare($rejected_query);
$rejected_stmt->execute();
$stats['rejected'] = $rejected_stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Gender distribution
$male_query = "SELECT COUNT(*) as count FROM students WHERE gender = 'male'";
$male_stmt = $db->prepare($male_query);
$male_stmt->execute();
$stats['male'] = $male_stmt->fetch(PDO::FETCH_ASSOC)['count'];

$female_query = "SELECT COUNT(*) as count FROM students WHERE gender = 'female'";
$female_stmt = $db->prepare($female_query);
$female_stmt->execute();
$stats['female'] = $female_stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Grade level distribution
$grade_query = "SELECT grade_level, COUNT(*) as count FROM students GROUP BY grade_level ORDER BY grade_level";
$grade_stmt = $db->prepare($grade_query);
$grade_stmt->execute();
$grade_stats = $grade_stmt->fetchAll(PDO::FETCH_ASSOC);

// Program distribution
$program_query = "SELECT program, COUNT(*) as count FROM students GROUP BY program ORDER BY count DESC";
$program_stmt = $db->prepare($program_query);
$program_stmt->execute();
$program_stats = $program_stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent enrollments (last 7 days)
$recent_query = "SELECT * FROM students WHERE enrollment_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY enrollment_date DESC";
$recent_stmt = $db->prepare($recent_query);
$recent_stmt->execute();
$recent_enrollments = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">
                <i class="fas fa-graduation-cap"></i> Student Enrollment System
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user"></i> Admin Panel
                </span>
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-bar"></i> Enrollment Reports
                        </h4>
                        <a href="../dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Overall Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="card stats-card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <h3><?php echo $stats['total']; ?></h3>
                                        <p class="mb-0">Total Students</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stats-card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <h3><?php echo $stats['enrolled']; ?></h3>
                                        <p class="mb-0">Enrolled</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stats-card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                        <h3><?php echo $stats['pending']; ?></h3>
                                        <p class="mb-0">Pending</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stats-card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                                        <h3><?php echo $stats['rejected']; ?></h3>
                                        <p class="mb-0">Rejected</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Gender Distribution Chart -->
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-venus-mars"></i> Gender Distribution</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="genderChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enrollment Status Chart -->
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Enrollment Status</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="statusChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Grade Level Distribution -->
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Grade Level Distribution</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Grade Level</th>
                                                        <th>Count</th>
                                                        <th>Percentage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($grade_stats as $grade): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($grade['grade_level']); ?></td>
                                                            <td><?php echo $grade['count']; ?></td>
                                                            <td>
                                                                <?php 
                                                                $percentage = $stats['total'] > 0 ? round(($grade['count'] / $stats['total']) * 100, 1) : 0;
                                                                echo $percentage . '%';
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Program Distribution -->
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-book"></i> Program Distribution</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Program</th>
                                                        <th>Count</th>
                                                        <th>Percentage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($program_stats as $program): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($program['program']); ?></td>
                                                            <td><?php echo $program['count']; ?></td>
                                                            <td>
                                                                <?php 
                                                                $percentage = $stats['total'] > 0 ? round(($program['count'] / $stats['total']) * 100, 1) : 0;
                                                                echo $percentage . '%';
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Enrollments -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Enrollments (Last 7 Days)</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($recent_enrollments)): ?>
                                            <div class="text-center text-muted py-3">
                                                <i class="fas fa-clock fa-2x mb-2"></i>
                                                <p>No recent enrollments</p>
                                            </div>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th>Student ID</th>
                                                            <th>Name</th>
                                                            <th>Email</th>
                                                            <th>Grade Level</th>
                                                            <th>Program</th>
                                                            <th>Status</th>
                                                            <th>Enrollment Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($recent_enrollments as $enrollment): ?>
                                                            <tr>
                                                                <td><strong><?php echo htmlspecialchars($enrollment['student_id']); ?></strong></td>
                                                                <td><?php echo htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?></td>
                                                                <td><?php echo htmlspecialchars($enrollment['email']); ?></td>
                                                                <td><?php echo htmlspecialchars($enrollment['grade_level']); ?></td>
                                                                <td><?php echo htmlspecialchars($enrollment['program']); ?></td>
                                                                <td>
                                                                    <span class="badge bg-<?php 
                                                                        echo $enrollment['enrollment_status'] == 'enrolled' ? 'success' : 
                                                                             ($enrollment['enrollment_status'] == 'pending' ? 'warning' : 'danger'); 
                                                                    ?>">
                                                                        <?php echo ucfirst($enrollment['enrollment_status']); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gender Distribution Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [<?php echo $stats['male']; ?>, <?php echo $stats['female']; ?>],
                    backgroundColor: ['#007bff', '#e83e8c'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Enrollment Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Enrolled', 'Pending', 'Rejected'],
                datasets: [{
                    data: [<?php echo $stats['enrolled']; ?>, <?php echo $stats['pending']; ?>, <?php echo $stats['rejected']; ?>],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
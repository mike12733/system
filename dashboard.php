<?php
require_once 'includes/functions.php';
requireLogin();

$stats = getDashboardStats();
$announcements = getRecentAnnouncements();

// Get current student's grade level for schedule
$gradeLevel = 'Grade 7'; // Default
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT grade_level FROM students WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $student = $stmt->fetch();
    if ($student) {
        $gradeLevel = $student['grade_level'];
    }
}

$schedule = getStudentSchedule($gradeLevel);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Online Enrollment System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h2>📚 Online Enrollment</h2>
            </div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <?php if (isAdmin()): ?>
                    <a href="admin.php" class="nav-link">Admin Panel</a>
                <?php endif; ?>
                <a href="register.php" class="nav-link">Register</a>
                <div class="nav-user">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1>Student Dashboard</h1>
            <p>Welcome to your enrollment dashboard</p>
        </div>

        <!-- Statistics Tiles -->
        <div class="stats-grid">
            <div class="stat-tile stat-enrolled">
                <div class="stat-icon">👨‍🎓</div>
                <div class="stat-content">
                    <h3><?php echo $stats['enrolled']; ?></h3>
                    <p>Enrolled Students</p>
                </div>
            </div>
            
            <div class="stat-tile stat-pending">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <h3><?php echo $stats['pending']; ?></h3>
                    <p>Pending Applicants</p>
                </div>
            </div>
            
            <div class="stat-tile stat-male">
                <div class="stat-icon">👨</div>
                <div class="stat-content">
                    <h3><?php echo $stats['male']; ?></h3>
                    <p>Male Students</p>
                </div>
            </div>
            
            <div class="stat-tile stat-female">
                <div class="stat-icon">👩</div>
                <div class="stat-content">
                    <h3><?php echo $stats['female']; ?></h3>
                    <p>Female Students</p>
                </div>
            </div>
        </div>

        <!-- Class Schedule -->
        <div class="dashboard-section">
            <h2>📅 Class Schedule (<?php echo $gradeLevel; ?>)</h2>
            <div class="table-container">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Time</th>
                            <th>Days</th>
                            <th>Room</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($schedule)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No schedule available for your grade level.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($schedule as $class): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($class['subject_name']); ?></td>
                                    <td><?php echo formatTime($class['time_start']) . ' - ' . formatTime($class['time_end']); ?></td>
                                    <td><?php echo htmlspecialchars($class['days']); ?></td>
                                    <td><?php echo htmlspecialchars($class['room']); ?></td>
                                    <td><?php echo htmlspecialchars($class['teacher']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Announcements -->
        <div class="dashboard-section">
            <h2>📢 Recent Announcements</h2>
            <div class="announcements-container">
                <?php if (empty($announcements)): ?>
                    <div class="announcement-item">
                        <h4>No announcements</h4>
                        <p>There are no recent announcements at this time.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-item">
                            <div class="announcement-header">
                                <h4><?php echo htmlspecialchars($announcement['title']); ?></h4>
                                <span class="announcement-date"><?php echo formatDate($announcement['created_at']); ?></span>
                            </div>
                            <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                            <small>Posted by: <?php echo htmlspecialchars($announcement['username']); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh stats every 30 seconds
        setInterval(function() {
            fetch('api/get_stats.php')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.stat-enrolled h3').textContent = data.enrolled;
                    document.querySelector('.stat-pending h3').textContent = data.pending;
                    document.querySelector('.stat-male h3').textContent = data.male;
                    document.querySelector('.stat-female h3').textContent = data.female;
                })
                .catch(error => console.error('Error fetching stats:', error));
        }, 30000);
    </script>
</body>
</html>
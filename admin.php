<?php
require_once 'includes/functions.php';
requireAdmin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                $studentId = (int)$_POST['student_id'];
                $status = sanitize($_POST['status']);
                if (updateStudentStatus($studentId, $status)) {
                    $success = 'Student status updated successfully.';
                } else {
                    $error = 'Failed to update student status.';
                }
                break;
                
            case 'add_announcement':
                $title = sanitize($_POST['title']);
                $content = sanitize($_POST['content']);
                if (!empty($title) && !empty($content)) {
                    if (addAnnouncement($title, $content, $_SESSION['user_id'])) {
                        $success = 'Announcement added successfully.';
                    } else {
                        $error = 'Failed to add announcement.';
                    }
                } else {
                    $error = 'Title and content are required.';
                }
                break;
        }
    }
}

$students = getAllStudents();
$announcements = getRecentAnnouncements(10);
$stats = getDashboardStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Online Enrollment System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h2>📚 Online Enrollment - Admin</h2>
            </div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="admin.php" class="nav-link active">Admin Panel</a>
                <a href="register.php" class="nav-link">Register</a>
                <div class="nav-user">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="admin-header">
            <h1>🔧 Admin Panel</h1>
            <p>Manage students and announcements</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Quick Stats -->
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
                    <p>Pending Applications</p>
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

        <!-- Student Management -->
        <div class="admin-section">
            <h2>👥 Student Management</h2>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Grade Level</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No students found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['grade_level']); ?></td>
                                    <td><?php echo htmlspecialchars($student['program']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $student['status']; ?>">
                                            <?php echo ucfirst($student['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatDate($student['created_at']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <select name="status" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $student['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="enrolled" <?php echo $student['status'] == 'enrolled' ? 'selected' : ''; ?>>Enrolled</option>
                                                <option value="rejected" <?php echo $student['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Announcement -->
        <div class="admin-section">
            <h2>📢 Add New Announcement</h2>
            <form method="POST" class="announcement-form">
                <input type="hidden" name="action" value="add_announcement">
                
                <div class="form-group">
                    <label for="title">Announcement Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Announcement Content</label>
                    <textarea id="content" name="content" rows="4" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    📢 Post Announcement
                </button>
            </form>
        </div>

        <!-- Recent Announcements -->
        <div class="admin-section">
            <h2>📋 Recent Announcements</h2>
            <div class="announcements-container">
                <?php if (empty($announcements)): ?>
                    <div class="announcement-item">
                        <h4>No announcements</h4>
                        <p>No announcements have been posted yet.</p>
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
            location.reload();
        }, 30000);

        // Confirmation for status changes
        document.querySelectorAll('select[name="status"]').forEach(select => {
            select.addEventListener('change', function(e) {
                if (!confirm('Are you sure you want to change this student\'s status?')) {
                    e.preventDefault();
                    this.value = this.defaultValue;
                }
            });
        });
    </script>
</body>
</html>
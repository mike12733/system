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

$success = '';
$error = '';

// Handle student status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $student_id = $_POST['student_id'] ?? '';
    $action = $_POST['action'];
    
    if ($action == 'approve') {
        $query = "UPDATE students SET enrollment_status = 'enrolled' WHERE id = ?";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$student_id])) {
            $success = 'Student enrollment approved successfully!';
        } else {
            $error = 'Failed to approve enrollment';
        }
    } elseif ($action == 'reject') {
        $query = "UPDATE students SET enrollment_status = 'rejected' WHERE id = ?";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$student_id])) {
            $success = 'Student enrollment rejected';
        } else {
            $error = 'Failed to reject enrollment';
        }
    } elseif ($action == 'delete') {
        $query = "DELETE FROM students WHERE id = ?";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$student_id])) {
            $success = 'Student deleted successfully!';
        } else {
            $error = 'Failed to delete student';
        }
    }
}

// Get all students
$query = "SELECT * FROM students ORDER BY enrollment_date DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Admin Panel</title>
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
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-enrolled {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
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
                            <i class="fas fa-users"></i> Manage Students
                        </h4>
                        <a href="../dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($students)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h5>No students found</h5>
                                <p>No students have registered yet.</p>
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
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($student['student_id']); ?></strong>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo ucfirst($student['gender']); ?> • 
                                                        <?php echo date('M d, Y', strtotime($student['date_of_birth'])); ?>
                                                    </small>
                                                </td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td><?php echo htmlspecialchars($student['grade_level']); ?></td>
                                                <td><?php echo htmlspecialchars($student['program']); ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo $student['enrollment_status']; ?>">
                                                        <?php echo ucfirst($student['enrollment_status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo date('M d, Y', strtotime($student['enrollment_date'])); ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if ($student['enrollment_status'] == 'pending'): ?>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                                                <input type="hidden" name="action" value="approve">
                                                                <button type="submit" class="btn btn-success btn-sm" 
                                                                        onclick="return confirm('Approve this student enrollment?')">
                                                                    <i class="fas fa-check"></i> Approve
                                                                </button>
                                                            </form>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                                                <input type="hidden" name="action" value="reject">
                                                                <button type="submit" class="btn btn-warning btn-sm" 
                                                                        onclick="return confirm('Reject this student enrollment?')">
                                                                    <i class="fas fa-times"></i> Reject
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                        
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    onclick="return confirm('Are you sure you want to delete this student?')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
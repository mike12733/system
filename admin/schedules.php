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

// Handle schedule actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'add') {
            $subject = trim($_POST['subject'] ?? '');
            $day_of_week = $_POST['day_of_week'] ?? '';
            $start_time = $_POST['start_time'] ?? '';
            $end_time = $_POST['end_time'] ?? '';
            $room = trim($_POST['room'] ?? '');
            $grade_level = $_POST['grade_level'] ?? '';
            
            if (empty($subject) || empty($day_of_week) || empty($start_time) || empty($end_time) || empty($room) || empty($grade_level)) {
                $error = 'All fields are required';
            } else {
                $query = "INSERT INTO class_schedules (subject, day_of_week, start_time, end_time, room, grade_level) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                if ($stmt->execute([$subject, $day_of_week, $start_time, $end_time, $room, $grade_level])) {
                    $success = 'Class schedule added successfully!';
                } else {
                    $error = 'Failed to add class schedule';
                }
            }
        } elseif ($action == 'delete') {
            $schedule_id = $_POST['schedule_id'] ?? '';
            $query = "DELETE FROM class_schedules WHERE id = ?";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$schedule_id])) {
                $success = 'Class schedule deleted successfully!';
            } else {
                $error = 'Failed to delete class schedule';
            }
        }
    }
}

// Get all class schedules
$query = "SELECT * FROM class_schedules ORDER BY day_of_week, start_time";
$stmt = $db->prepare($query);
$stmt->execute();
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Class Schedules - Admin Panel</title>
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
                            <i class="fas fa-calendar"></i> Manage Class Schedules
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

                        <!-- Add New Schedule -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-plus"></i> Add New Class Schedule</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="add">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="subject" class="form-label">Subject *</label>
                                            <input type="text" class="form-control" id="subject" name="subject" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="day_of_week" class="form-label">Day of Week *</label>
                                            <select class="form-control" id="day_of_week" name="day_of_week" required>
                                                <option value="">Select Day</option>
                                                <option value="Monday">Monday</option>
                                                <option value="Tuesday">Tuesday</option>
                                                <option value="Wednesday">Wednesday</option>
                                                <option value="Thursday">Thursday</option>
                                                <option value="Friday">Friday</option>
                                                <option value="Saturday">Saturday</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="start_time" class="form-label">Start Time *</label>
                                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="end_time" class="form-label">End Time *</label>
                                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="room" class="form-label">Room *</label>
                                            <input type="text" class="form-control" id="room" name="room" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="grade_level" class="form-label">Grade Level *</label>
                                            <select class="form-control" id="grade_level" name="grade_level" required>
                                                <option value="">Select Grade Level</option>
                                                <option value="Grade 7">Grade 7</option>
                                                <option value="Grade 8">Grade 8</option>
                                                <option value="Grade 9">Grade 9</option>
                                                <option value="Grade 10">Grade 10</option>
                                                <option value="Grade 11">Grade 11</option>
                                                <option value="Grade 12">Grade 12</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Add Schedule
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Existing Schedules -->
                        <h5 class="mb-3"><i class="fas fa-list"></i> Existing Class Schedules</h5>
                        
                        <?php if (empty($schedules)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                <h5>No class schedules found</h5>
                                <p>No class schedules have been created yet.</p>
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
                                            <th>Actions</th>
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
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                                onclick="return confirm('Are you sure you want to delete this class schedule?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
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
<?php
session_start();
require_once 'config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $grade_level = $_POST['grade_level'] ?? '';
    $program = $_POST['program'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($first_name)) $errors[] = 'First name is required';
    if (empty($last_name)) $errors[] = 'Last name is required';
    if (empty($date_of_birth)) $errors[] = 'Date of birth is required';
    if (empty($gender)) $errors[] = 'Gender is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
    if (empty($grade_level)) $errors[] = 'Grade level is required';
    if (empty($program)) $errors[] = 'Program is required';
    
    if (empty($errors)) {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if email already exists
        $check_query = "SELECT id FROM students WHERE email = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$email]);
        
        if ($check_stmt->fetch()) {
            $error = 'Email already exists in our system';
        } else {
            // Generate student ID
            $student_id = 'STU' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Insert student
            $query = "INSERT INTO students (student_id, first_name, last_name, date_of_birth, gender, email, phone, grade_level, program) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$student_id, $first_name, $last_name, $date_of_birth, $gender, $email, $phone, $grade_level, $program])) {
                $success = 'Registration successful! Your Student ID is: ' . $student_id;
                
                // Clear form data
                $_POST = array();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Get programs for dropdown
$database = new Database();
$db = $database->getConnection();
$programs_query = "SELECT name FROM programs ORDER BY name";
$programs_stmt = $db->prepare($programs_query);
$programs_stmt->execute();
$programs = $programs_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .registration-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }
        .registration-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .registration-body {
            padding: 40px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .section-title {
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="registration-card">
            <div class="registration-header">
                <h2><i class="fas fa-user-plus"></i> Student Registration</h2>
                <p class="mb-0">Complete your enrollment application</p>
            </div>
            
            <div class="registration-body">
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <br><br>
                        <a href="index.php" class="btn btn-primary">Login Now</a>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="section-title">
                                <i class="fas fa-user"></i> Personal Information
                            </h4>
                            
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                       value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender *</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" <?php echo ($_POST['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo ($_POST['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h4 class="section-title">
                                <i class="fas fa-address-book"></i> Contact Information
                            </h4>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            
                            <h4 class="section-title">
                                <i class="fas fa-graduation-cap"></i> Enrollment Information
                            </h4>
                            
                            <div class="mb-3">
                                <label for="grade_level" class="form-label">Grade Level *</label>
                                <select class="form-control" id="grade_level" name="grade_level" required>
                                    <option value="">Select Grade Level</option>
                                    <option value="Grade 7" <?php echo ($_POST['grade_level'] ?? '') == 'Grade 7' ? 'selected' : ''; ?>>Grade 7</option>
                                    <option value="Grade 8" <?php echo ($_POST['grade_level'] ?? '') == 'Grade 8' ? 'selected' : ''; ?>>Grade 8</option>
                                    <option value="Grade 9" <?php echo ($_POST['grade_level'] ?? '') == 'Grade 9' ? 'selected' : ''; ?>>Grade 9</option>
                                    <option value="Grade 10" <?php echo ($_POST['grade_level'] ?? '') == 'Grade 10' ? 'selected' : ''; ?>>Grade 10</option>
                                    <option value="Grade 11" <?php echo ($_POST['grade_level'] ?? '') == 'Grade 11' ? 'selected' : ''; ?>>Grade 11</option>
                                    <option value="Grade 12" <?php echo ($_POST['grade_level'] ?? '') == 'Grade 12' ? 'selected' : ''; ?>>Grade 12</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="program" class="form-label">Program *</label>
                                <select class="form-control" id="program" name="program" required>
                                    <option value="">Select Program</option>
                                    <?php foreach ($programs as $program_name): ?>
                                        <option value="<?php echo htmlspecialchars($program_name); ?>" 
                                                <?php echo ($_POST['program'] ?? '') == $program_name ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($program_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-register">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                        <a href="index.php" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
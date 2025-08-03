<?php
require_once 'includes/functions.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'first_name' => sanitize($_POST['first_name']),
        'last_name' => sanitize($_POST['last_name']),
        'date_of_birth' => sanitize($_POST['date_of_birth']),
        'gender' => sanitize($_POST['gender']),
        'email' => sanitize($_POST['email']),
        'phone' => sanitize($_POST['phone']),
        'grade_level' => sanitize($_POST['grade_level']),
        'program' => sanitize($_POST['program'])
    ];
    
    // Validation
    $errors = [];
    
    if (empty($data['first_name'])) {
        $errors[] = 'First name is required.';
    }
    if (empty($data['last_name'])) {
        $errors[] = 'Last name is required.';
    }
    if (empty($data['date_of_birth'])) {
        $errors[] = 'Date of birth is required.';
    }
    if (empty($data['gender'])) {
        $errors[] = 'Gender is required.';
    }
    if (empty($data['email'])) {
        $errors[] = 'Email is required.';
    } elseif (!validateEmail($data['email'])) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (empty($data['phone'])) {
        $errors[] = 'Phone number is required.';
    }
    if (empty($data['grade_level'])) {
        $errors[] = 'Grade level is required.';
    }
    if (empty($data['program'])) {
        $errors[] = 'Program is required.';
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            $errors[] = 'Email address is already registered.';
        }
    }
    
    if (empty($errors)) {
        $result = registerStudent($data);
        if ($result['success']) {
            $success = 'Registration successful! Your Student ID is: ' . $result['student_id'] . '. You can now login with your email address.';
            // Clear form data
            $data = [];
        } else {
            $error = 'Registration failed: ' . $result['error'];
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Online Enrollment System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h2>📚 Online Enrollment</h2>
            </div>
            <div class="nav-menu">
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php" class="nav-link">Admin Panel</a>
                    <?php endif; ?>
                    <a href="register.php" class="nav-link active">Register</a>
                    <div class="nav-user">
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="nav-link active">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="registration-container">
            <div class="registration-header">
                <h1>Student Registration Form</h1>
                <p>Fill out this form to enroll in our high school</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="registration-form">
                <!-- Personal Information Section -->
                <div class="form-section">
                    <h3>📋 Personal Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required 
                                   value="<?php echo isset($data['first_name']) ? htmlspecialchars($data['first_name']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required 
                                   value="<?php echo isset($data['last_name']) ? htmlspecialchars($data['last_name']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth *</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" required 
                                   value="<?php echo isset($data['date_of_birth']) ? $data['date_of_birth'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo (isset($data['gender']) && $data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (isset($data['gender']) && $data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section">
                    <h3>📞 Contact Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required 
                                   value="<?php echo isset($data['phone']) ? htmlspecialchars($data['phone']) : ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- Enrollment Information Section -->
                <div class="form-section">
                    <h3>🎓 Enrollment Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="grade_level">Grade Level *</label>
                            <select id="grade_level" name="grade_level" required>
                                <option value="">Select Grade Level</option>
                                <option value="Grade 7" <?php echo (isset($data['grade_level']) && $data['grade_level'] == 'Grade 7') ? 'selected' : ''; ?>>Grade 7</option>
                                <option value="Grade 8" <?php echo (isset($data['grade_level']) && $data['grade_level'] == 'Grade 8') ? 'selected' : ''; ?>>Grade 8</option>
                                <option value="Grade 9" <?php echo (isset($data['grade_level']) && $data['grade_level'] == 'Grade 9') ? 'selected' : ''; ?>>Grade 9</option>
                                <option value="Grade 10" <?php echo (isset($data['grade_level']) && $data['grade_level'] == 'Grade 10') ? 'selected' : ''; ?>>Grade 10</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="program">Program *</label>
                            <select id="program" name="program" required>
                                <option value="">Select Program</option>
                                <option value="General Academic Strand (GAS)" <?php echo (isset($data['program']) && $data['program'] == 'General Academic Strand (GAS)') ? 'selected' : ''; ?>>General Academic Strand (GAS)</option>
                                <option value="Science, Technology, Engineering and Mathematics (STEM)" <?php echo (isset($data['program']) && $data['program'] == 'Science, Technology, Engineering and Mathematics (STEM)') ? 'selected' : ''; ?>>Science, Technology, Engineering and Mathematics (STEM)</option>
                                <option value="Humanities and Social Sciences (HUMSS)" <?php echo (isset($data['program']) && $data['program'] == 'Humanities and Social Sciences (HUMSS)') ? 'selected' : ''; ?>>Humanities and Social Sciences (HUMSS)</option>
                                <option value="Accountancy, Business and Management (ABM)" <?php echo (isset($data['program']) && $data['program'] == 'Accountancy, Business and Management (ABM)') ? 'selected' : ''; ?>>Accountancy, Business and Management (ABM)</option>
                                <option value="Technical-Vocational-Livelihood (TVL)" <?php echo (isset($data['program']) && $data['program'] == 'Technical-Vocational-Livelihood (TVL)') ? 'selected' : ''; ?>>Technical-Vocational-Livelihood (TVL)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large">
                        📝 Register Student
                    </button>
                    <p class="form-note">* Required fields</p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Form validation
        document.querySelector('.registration-form').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#e74c3c';
                    isValid = false;
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>
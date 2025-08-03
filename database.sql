-- Online Enrollment System Database
-- Import this file into phpMyAdmin to set up the database

CREATE DATABASE IF NOT EXISTS online_enrollment;
USE online_enrollment;

-- Users table (for authentication)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    grade_level VARCHAR(10) NOT NULL,
    program VARCHAR(100) NOT NULL,
    status ENUM('pending', 'enrolled', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Subjects table
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(10) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    grade_level VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Class schedules table
CREATE TABLE class_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    time_start TIME NOT NULL,
    time_end TIME NOT NULL,
    days VARCHAR(50) NOT NULL,
    room VARCHAR(20) NOT NULL,
    teacher VARCHAR(100) NOT NULL,
    grade_level VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Student enrollments table
CREATE TABLE student_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Announcements table
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample subjects
INSERT INTO subjects (subject_code, subject_name, grade_level) VALUES
('MATH101', 'Mathematics 1', 'Grade 7'),
('ENG101', 'English 1', 'Grade 7'),
('SCI101', 'Science 1', 'Grade 7'),
('FIL101', 'Filipino 1', 'Grade 7'),
('HIST101', 'History 1', 'Grade 7'),
('MATH201', 'Mathematics 2', 'Grade 8'),
('ENG201', 'English 2', 'Grade 8'),
('SCI201', 'Science 2', 'Grade 8'),
('FIL201', 'Filipino 2', 'Grade 8'),
('HIST201', 'History 2', 'Grade 8'),
('MATH301', 'Mathematics 3', 'Grade 9'),
('ENG301', 'English 3', 'Grade 9'),
('SCI301', 'Science 3', 'Grade 9'),
('FIL301', 'Filipino 3', 'Grade 9'),
('HIST301', 'History 3', 'Grade 9'),
('MATH401', 'Mathematics 4', 'Grade 10'),
('ENG401', 'English 4', 'Grade 10'),
('SCI401', 'Science 4', 'Grade 10'),
('FIL401', 'Filipino 4', 'Grade 10'),
('HIST401', 'History 4', 'Grade 10');

-- Insert sample class schedules
INSERT INTO class_schedules (subject_id, time_start, time_end, days, room, teacher, grade_level) VALUES
(1, '08:00:00', '09:00:00', 'Monday, Wednesday, Friday', 'Room 101', 'Mrs. Santos', 'Grade 7'),
(2, '09:00:00', '10:00:00', 'Monday, Wednesday, Friday', 'Room 102', 'Mr. Garcia', 'Grade 7'),
(3, '10:00:00', '11:00:00', 'Tuesday, Thursday', 'Room 103', 'Ms. Cruz', 'Grade 7'),
(4, '11:00:00', '12:00:00', 'Monday, Wednesday, Friday', 'Room 104', 'Mrs. Reyes', 'Grade 7'),
(5, '13:00:00', '14:00:00', 'Tuesday, Thursday', 'Room 105', 'Mr. Lopez', 'Grade 7'),
(6, '08:00:00', '09:00:00', 'Monday, Wednesday, Friday', 'Room 201', 'Mrs. Dela Cruz', 'Grade 8'),
(7, '09:00:00', '10:00:00', 'Monday, Wednesday, Friday', 'Room 202', 'Mr. Mendoza', 'Grade 8'),
(8, '10:00:00', '11:00:00', 'Tuesday, Thursday', 'Room 203', 'Ms. Ramos', 'Grade 8'),
(9, '11:00:00', '12:00:00', 'Monday, Wednesday, Friday', 'Room 204', 'Mrs. Torres', 'Grade 8'),
(10, '13:00:00', '14:00:00', 'Tuesday, Thursday', 'Room 205', 'Mr. Flores', 'Grade 8');

-- Insert sample announcements
INSERT INTO announcements (title, content, created_by) VALUES
('Welcome to Online Enrollment', 'Welcome to our new online enrollment system. Students can now register for classes online without visiting the school.', 1),
('Enrollment Period Extended', 'The enrollment period has been extended until the end of this month. Please complete your registration as soon as possible.', 1),
('New Academic Year Guidelines', 'Please review the new academic year guidelines and requirements posted on the school website.', 1);
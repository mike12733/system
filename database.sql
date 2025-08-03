-- Student Enrollment System Database Schema
-- For import to phpMyAdmin in XAMPP

-- Create database
CREATE DATABASE IF NOT EXISTS student_enrollment_system;
USE student_enrollment_system;

-- Users table for login system
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'registrar', 'teacher', 'student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    grade_level ENUM('Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12') NOT NULL,
    program VARCHAR(100) NOT NULL,
    enrollment_status ENUM('pending', 'enrolled', 'rejected') DEFAULT 'pending',
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Programs table
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Class schedules table
CREATE TABLE class_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(100) NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room VARCHAR(20) NOT NULL,
    grade_level ENUM('Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Announcements table
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (username, password, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@school.com', 'admin');

-- Insert sample programs
INSERT INTO programs (name, description) VALUES 
('Science, Technology, Engineering and Mathematics (STEM)', 'STEM program for students interested in science and technology'),
('Accountancy, Business and Management (ABM)', 'ABM program for students interested in business and management'),
('Humanities and Social Sciences (HUMSS)', 'HUMSS program for students interested in humanities and social sciences'),
('General Academic Strand (GAS)', 'GAS program for students with diverse academic interests');

-- Insert sample class schedules
INSERT INTO class_schedules (subject, day_of_week, start_time, end_time, room, grade_level) VALUES 
('Mathematics', 'Monday', '08:00:00', '09:00:00', 'Room 101', 'Grade 7'),
('English', 'Monday', '09:00:00', '10:00:00', 'Room 102', 'Grade 7'),
('Science', 'Tuesday', '08:00:00', '09:00:00', 'Room 103', 'Grade 7'),
('History', 'Tuesday', '09:00:00', '10:00:00', 'Room 104', 'Grade 7'),
('Mathematics', 'Monday', '08:00:00', '09:00:00', 'Room 201', 'Grade 8'),
('English', 'Monday', '09:00:00', '10:00:00', 'Room 202', 'Grade 8'),
('Science', 'Tuesday', '08:00:00', '09:00:00', 'Room 203', 'Grade 8'),
('History', 'Tuesday', '09:00:00', '10:00:00', 'Room 204', 'Grade 8');

-- Insert sample announcements
INSERT INTO announcements (title, content, author_id) VALUES 
('Welcome to the New School Year!', 'Welcome all students to the new academic year. Please check your class schedules and be ready for the first day of classes.', 1),
('Enrollment Deadline Reminder', 'Please complete your enrollment process before the deadline. Contact the registrar for any questions.', 1),
('School Events This Month', 'Check the bulletin board for upcoming school events and activities this month.', 1);
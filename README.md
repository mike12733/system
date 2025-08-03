# Student Enrollment System

A comprehensive PHP-based student enrollment system that allows high school students to enroll online without needing to walk-in. The system provides a complete solution for managing student registrations, class schedules, announcements, and administrative functions.

## 🎯 System Purpose

This system eliminates the need for students to physically visit the school for enrollment. Students can now:
- Register online through a user-friendly form
- View class schedules and announcements
- Track their enrollment status
- Access all enrollment information digitally

## 🚀 Features

### Student Dashboard
- **4 Colored Tiles** displaying:
  - Enrolled students count
  - Pending applicants count
  - Male students count
  - Female students count
- **Class Schedule Table** with subjects, times, days, and rooms
- **Recent Announcements** section
- **Real-time Statistics** that auto-update

### Student Registration Form
- **Personal Information**: First Name, Last Name, Date of Birth, Gender
- **Contact Information**: Email, Phone
- **Enrollment Information**: Student ID (auto-generated), Grade Level, Program (dropdown)
- **Form Validation**: Required fields, valid email format
- **Success Confirmation**: Redirects to confirmation page with Student ID

### Admin Panel
- **Student Management**: Add, edit, delete students, approve/reject enrollments
- **Announcement Management**: Create, edit, delete announcements
- **Schedule Management**: Add, edit, delete class schedules
- **Reports & Analytics**: View enrollment statistics with charts
- **Role-based Access**: Secure login system with admin privileges

### Security Features
- **Secure Login System** with role-based access
- **Password Hashing** for user security
- **Session Management** for user authentication
- **SQL Injection Prevention** using prepared statements
- **Input Validation** and sanitization

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL (compatible with XAMPP)
- **Charts**: Chart.js for analytics
- **Icons**: Font Awesome 6

## 📋 Requirements

- XAMPP (Apache + MySQL + PHP)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Safari, Edge)

## 🚀 Installation Guide

### Step 1: Setup XAMPP
1. Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start Apache and MySQL services in XAMPP Control Panel

### Step 2: Import Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `student_enrollment_system`
3. Import the `database.sql` file into the database
4. The database will be created with sample data

### Step 3: Configure Database Connection
1. Open `config/database.php`
2. Verify the database settings match your XAMPP configuration:
   ```php
   private $host = 'localhost';
   private $db_name = 'student_enrollment_system';
   private $username = 'root';
   private $password = '';
   ```

### Step 4: Deploy Application
1. Copy all files to your XAMPP `htdocs` folder
2. Navigate to: `http://localhost/your-project-folder`

### Step 5: Access the System
- **Login URL**: `http://localhost/your-project-folder`
- **Default Admin Account**:
  - Username: `admin`
  - Password: `password`

## 📁 File Structure

```
student-enrollment-system/
├── config/
│   └── database.php          # Database configuration
├── admin/
│   ├── students.php          # Student management
│   ├── announcements.php     # Announcement management
│   ├── schedules.php         # Schedule management
│   └── reports.php           # Analytics and reports
├── index.php                 # Login page
├── register.php              # Student registration
├── dashboard.php             # Main dashboard
├── logout.php                # Logout functionality
├── database.sql              # Database schema
└── README.md                 # This file
```

## 👥 User Roles

### Admin
- Full access to all features
- Manage students, announcements, schedules
- View reports and analytics
- Approve/reject student enrollments

### Students
- Register for enrollment
- View class schedules
- Read announcements
- Track enrollment status

## 🎨 Features in Detail

### Dashboard Statistics
- **Real-time Counts**: All statistics update automatically when new students register
- **Color-coded Tiles**: Each statistic has a distinct color for easy identification
- **Responsive Design**: Works on desktop, tablet, and mobile devices

### Registration Form
- **Auto-generated Student ID**: Format: STU + Year + Random 4-digit number
- **Email Validation**: Prevents duplicate registrations
- **Required Field Validation**: Ensures all necessary information is provided
- **Success Feedback**: Clear confirmation with Student ID

### Class Schedule
- **Organized by Day**: Schedules are sorted by day of the week
- **Time Format**: 12-hour format with AM/PM
- **Room Information**: Clear room assignments
- **Grade Level Filtering**: Schedules organized by grade level

### Admin Functions
- **Student Approval System**: Admin can approve or reject pending enrollments
- **Bulk Management**: View all students in a table format
- **Status Tracking**: Track enrollment status (pending, enrolled, rejected)
- **Data Export**: View all data in organized tables

## 🔒 Security Features

- **Password Protection**: All admin functions require login
- **Session Management**: Secure session handling
- **SQL Injection Prevention**: Prepared statements for all database queries
- **Input Sanitization**: All user inputs are sanitized
- **Role-based Access**: Different permissions for different user types

## 📊 Database Schema

The system uses a relational database with the following tables:
- `users`: Admin user accounts
- `students`: Student registration data
- `programs`: Available academic programs
- `class_schedules`: Class schedule information
- `announcements`: School announcements

## 🎯 Key Benefits

1. **Eliminates Walk-in Enrollment**: Students can enroll from anywhere
2. **Reduces Administrative Burden**: Automated enrollment process
3. **Real-time Tracking**: Instant updates on enrollment status
4. **Data Analytics**: Comprehensive reporting and statistics
5. **User-friendly Interface**: Modern, responsive design
6. **Secure System**: Protected user data and admin functions

## 🚀 Getting Started

1. Follow the installation guide above
2. Access the system at `http://localhost/your-project-folder`
3. Login with admin credentials or register as a new student
4. Explore all features and functionalities

## 📞 Support

For technical support or questions about the system, please refer to the documentation or contact the development team.

---

**Note**: This system is designed for educational purposes and can be customized for specific school requirements.
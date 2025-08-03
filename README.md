# Online Enrollment System

A comprehensive web-based enrollment system for high schools built with PHP and MySQL. This system allows students to register online for classes without having to visit the school physically.

## Features

### 🎓 Student Features
- **Online Registration**: Complete student registration form with validation
- **Student Dashboard**: View enrollment statistics, class schedules, and announcements
- **Colored Statistics Tiles**: Visual display of enrolled students, pending applicants, male/female student counts
- **Class Schedule**: Table format showing subjects, times, days, rooms, and teachers
- **Announcements**: Recent school announcements and updates

### 👨‍💼 Admin Features
- **Admin Panel**: Comprehensive management interface
- **Student Management**: View, approve, reject, and manage student applications
- **Announcement System**: Post and manage school announcements
- **Real-time Statistics**: Auto-updating dashboard statistics
- **Status Management**: Change student status (pending, enrolled, rejected)

### 🔐 Security Features
- **Role-based Authentication**: Admin and Student roles
- **Secure Login System**: Password hashing and session management
- **Form Validation**: Server-side and client-side validation
- **SQL Injection Protection**: Prepared statements and sanitized inputs

### 📱 Responsive Design
- **Mobile-friendly**: Works on desktop, tablet, and mobile devices
- **Modern UI**: Beautiful gradient design with glassmorphism effects
- **Smooth Animations**: CSS transitions and hover effects

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache (XAMPP compatible)

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server
- XAMPP (recommended for local development)

## Installation Instructions

### 1. Download and Setup XAMPP
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP on your computer
3. Start Apache and MySQL services from XAMPP Control Panel

### 2. Setup the Project
1. Copy all project files to your XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\online-enrollment\
   ```

### 3. Database Setup
1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Create a new database named `online_enrollment`
3. Import the database schema:
   - Click on the `online_enrollment` database
   - Go to the "Import" tab
   - Choose the `database.sql` file from the project root
   - Click "Go" to import

### 4. Configuration
1. Open `config/database.php` and verify the database settings:
   ```php
   $host = 'localhost';
   $dbname = 'online_enrollment';
   $username = 'root';
   $password = '';
   ```

### 5. Access the System
1. Open your web browser
2. Navigate to: `http://localhost/online-enrollment/`
3. You will be redirected to the login page

## Default Login Credentials

### Admin Account
- **Email**: admin@school.com
- **Password**: password

### Test Student Account
Create a new student account using the registration form, or use the admin panel to manage existing students.

## File Structure

```
online-enrollment/
│
├── config/
│   └── database.php          # Database configuration
│
├── includes/
│   └── functions.php         # Core functions and utilities
│
├── css/
│   └── style.css            # Main stylesheet
│
├── api/
│   └── get_stats.php        # API endpoint for statistics
│
├── database.sql             # Database schema and sample data
├── index.php               # Main entry point
├── login.php               # Login page
├── register.php            # Student registration form
├── dashboard.php           # Student dashboard
├── admin.php               # Admin panel
├── logout.php              # Logout script
└── README.md               # This file
```

## Usage Guide

### For Students
1. **Registration**: 
   - Click "Register here" on the login page
   - Fill out the complete registration form
   - Submit and receive your Student ID
   - Login with your email and password

2. **Dashboard**:
   - View enrollment statistics
   - Check your class schedule
   - Read recent announcements

### For Administrators
1. **Login** with admin credentials
2. **Student Management**:
   - View all student applications
   - Change student status (pending → enrolled/rejected)
   - Monitor registration statistics

3. **Announcements**:
   - Post new announcements
   - View recent announcements
   - Manage school communications

## Features in Detail

### Registration Form Sections
1. **Personal Information**: First Name, Last Name, Date of Birth, Gender
2. **Contact Information**: Email, Phone Number
3. **Enrollment Information**: Grade Level, Program (GAS, STEM, HUMSS, ABM, TVL)

### Dashboard Statistics
- **Enrolled Students**: Total number of enrolled students
- **Pending Applicants**: Students awaiting approval
- **Male Students**: Count of male students
- **Female Students**: Count of female students

### Class Schedule Display
- Subject names and codes
- Class times (start and end)
- Days of the week
- Room assignments
- Teacher names

## Customization

### Adding New Programs
Edit the `register.php` file and add new options to the program dropdown:
```php
<option value="New Program Name">New Program Name</option>
```

### Changing Colors and Styling
Modify the `css/style.css` file to customize:
- Color schemes
- Fonts
- Layout spacing
- Animation effects

### Adding New Features
Extend functionality by:
1. Adding new database tables
2. Creating new PHP functions in `includes/functions.php`
3. Building new pages following the existing structure

## Security Notes

- The default admin password should be changed in production
- All user inputs are sanitized and validated
- Passwords are hashed using PHP's `password_hash()` function
- Session management prevents unauthorized access
- SQL injection protection through prepared statements

## Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Verify MySQL is running in XAMPP
   - Check database credentials in `config/database.php`
   - Ensure database `online_enrollment` exists

2. **Page Not Found**:
   - Verify files are in the correct directory
   - Check Apache is running in XAMPP
   - Ensure URL is correct (`http://localhost/online-enrollment/`)

3. **Registration Not Working**:
   - Check database tables exist
   - Verify form validation
   - Check error messages in browser console

### Getting Help

If you encounter issues:
1. Check the XAMPP error logs
2. Verify database connection
3. Ensure all files are properly uploaded
4. Check browser console for JavaScript errors

## License

This project is created for educational purposes. Feel free to modify and adapt for your needs.

## Contributing

To contribute to this project:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

---

**Note**: This system is designed for educational use and may require additional security measures for production deployment.
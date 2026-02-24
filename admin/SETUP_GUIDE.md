# Department Management System - Complete Documentation

## 📋 Table of Contents
1. [System Overview](#system-overview)
2. [Installation Guide](#installation-guide)
3. [Security Features](#security-features)
4. [File Structure](#file-structure)
5. [Database Setup](#database-setup)
6. [Admin Panel Guide](#admin-panel-guide)
7. [Frontend Integration](#frontend-integration)
8. [Customization Guide](#customization-guide)
9. [Troubleshooting](#troubleshooting)

---

## 🎯 System Overview

This is a complete department management system with:
- **Secure Admin Panel** - Separate design from main website
- **Faculty Management** - CRUD operations with photo uploads
- **Department Pages** - Dynamic frontend pages
- **Results Tracking** - Display department results
- **Course Management** - Manage courses and seat capacity
- **Activity Logging** - Track all admin actions
- **CSRF Protection** - Secure against cross-site request forgery
- **SQL Injection Prevention** - Using PDO prepared statements

---

## 🚀 Installation Guide

### Step 1: Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- GD Library for image processing

### Step 2: Upload Files
1. Upload all files to your server
2. Ensure proper permissions:
```bash
chmod 755 -R department-system/
chmod 777 department-system/uploads/faculty/
```

### Step 3: Database Setup
1. Open phpMyAdmin or MySQL command line
2. Import the database:
```bash
mysql -u username -p college_db < database.sql
```

Or manually:
1. Create database: `college_db`
2. Run the SQL in `database.sql`

### Step 4: Configure Settings
Edit `config/config.php`:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'college_db');
define('DB_USER', 'your_username');  // Change this
define('DB_PASS', 'your_password');  // Change this

// Site Configuration
define('SITE_URL', 'https://yourwebsite.com');  // Change this
```

### Step 5: Test Installation
1. Visit: `http://yoursite.com/department-system/admin/login.php`
2. Default credentials:
   - Username: `admin`
   - Password: `admin123`
3. **IMPORTANT**: Change the password immediately after first login!

---

## 🔒 Security Features

### 1. **Password Hashing**
- Uses PHP's `password_hash()` with BCRYPT
- Passwords never stored in plain text

### 2. **SQL Injection Prevention**
- All queries use PDO prepared statements
- Parameters are bound separately

### 3. **CSRF Protection**
- Tokens generated for all forms
- Verified on submission
- Enable/disable in config

### 4. **File Upload Security**
- File type validation
- Size limit enforcement (5MB default)
- Only images allowed (JPG, PNG, GIF)
- Unique filename generation
- MIME type verification

### 5. **Session Security**
- Session timeout (1 hour default)
- Session regeneration on login
- Secure session configuration

### 6. **XSS Prevention**
- All output uses `htmlspecialchars()`
- User input sanitized

### 7. **Activity Logging**
- All admin actions logged
- IP address tracking
- Timestamp recording

---

## 📁 File Structure

```
department-system/
├── config/
│   └── config.php              # Main configuration
├── includes/
│   ├── Database.php            # Database class
│   ├── Auth.php                # Authentication class
│   ├── Faculty.php             # Faculty model
│   ├── Department.php          # Department model
│   └── FileUpload.php          # File upload helper
├── admin/
│   ├── css/
│   │   └── admin.css           # Admin panel styles
│   ├── includes/
│   │   └── sidebar.php         # Admin sidebar
│   ├── login.php               # Admin login
│   ├── index.php               # Admin dashboard
│   ├── faculty-add.php         # Add faculty
│   ├── faculty-list.php        # Faculty list
│   ├── faculty-edit.php        # Edit faculty
│   ├── faculty-delete.php      # Delete faculty
│   └── logout.php              # Logout
├── css/
│   └── department.css          # Frontend styles
├── uploads/
│   └── faculty/                # Faculty photos (777 permissions)
├── department.php              # Frontend department page
└── database.sql                # Database schema
```

---

## 💾 Database Setup

### Tables Created:

1. **departments** - Department information
2. **faculty** - Faculty member details
3. **courses** - Course information
4. **department_results** - Year-wise results
5. **admin_users** - Admin user accounts
6. **admin_logs** - Activity logging

### Default Data:
- Sample Economics department
- Default admin user (admin/admin123)
- Sample faculty members
- Sample courses and results

---

## 👤 Admin Panel Guide

### Login
- URL: `/admin/login.php`
- Default: admin / admin123

### Dashboard (`index.php`)
- Overview statistics
- Recent faculty list
- Quick actions

### Faculty Management

#### Add Faculty (`faculty-add.php`)
1. Click "Add New Faculty"
2. Fill in required fields:
   - Department *
   - Full Name *
   - Designation *
3. Optional fields:
   - Qualification
   - Email
   - Phone
   - Photo
   - Biography
   - Display Order
4. Click "Save Faculty"

#### Edit Faculty (`faculty-edit.php`)
1. Click "Edit" on any faculty
2. Update information
3. Upload new photo (optional)
4. Save changes

#### Delete Faculty (`faculty-delete.php`)
1. Click "Delete" on any faculty
2. Confirm deletion
3. Faculty is soft-deleted (can be restored)

---

## 🌐 Frontend Integration

### Display Department Page

**URL Format:**
```
http://yoursite.com/department.php?code=ECON
```

**Department Codes:**
- ECON - Economics
- CS - Computer Science
- (Add more in database)

### Integration with Main Website

Add to your navigation menu:

```html
<li class="nav-item">
    <a class="nav-link" href="department.php?code=ECON">Economics</a>
</li>
```

### Features:
- **3 Tabs**: Department, Faculty, Courses
- **Faculty Cards**: Displays all active faculty
- **Courses Table**: Shows available courses
- **Results Widget**: Last 2 years' results

---

## 🎨 Customization Guide

### Change Admin Panel Colors

Edit `admin/css/admin.css`:

```css
:root {
    --admin-primary: #2c3e50;     /* Dark blue */
    --admin-accent: #3498db;      /* Light blue */
    --admin-success: #27ae60;     /* Green */
    --admin-danger: #e74c3c;      /* Red */
}
```

### Change Department Page Colors

Edit `css/department.css`:

```css
.page-header {
    background: linear-gradient(135deg, #003366 0%, #0056b3 100%);
}
```

### Add Custom Fields

#### Step 1: Add to Database
```sql
ALTER TABLE faculty ADD COLUMN new_field VARCHAR(255);
```

#### Step 2: Update Faculty Model
Add to `includes/Faculty.php` in create/update methods.

#### Step 3: Update Admin Forms
Add field to `admin/faculty-add.php` and `admin/faculty-edit.php`.

---

## 🔧 Troubleshooting

### Common Issues:

#### 1. Cannot upload photos
**Solution:**
```bash
chmod 777 uploads/faculty/
```

#### 2. Database connection failed
**Solution:**
- Check credentials in `config/config.php`
- Verify MySQL is running
- Check database exists

#### 3. Session timeout too short/long
**Solution:**
Edit `config/config.php`:
```php
define('SESSION_TIMEOUT', 7200); // 2 hours
```

#### 4. CSRF token error
**Solution:**
- Clear browser cache
- Check `ENABLE_CSRF` in config
- Ensure forms have csrf_token field

#### 5. Photos not displaying
**Solution:**
- Check file path in database
- Verify file exists in uploads/faculty/
- Check file permissions

---

## 🔐 Security Best Practices

### For Production:

1. **Change Default Password**
```php
// Use this to generate new password hash:
echo password_hash('your_new_password', PASSWORD_BCRYPT);
```

2. **Update Config**
```php
define('DEBUG_MODE', false);  // Disable debug mode
```

3. **Use HTTPS**
- Install SSL certificate
- Force HTTPS in .htaccess

4. **Restrict Admin Access**
Add to admin folder `.htaccess`:
```apache
# Allow only specific IPs
Order Deny,Allow
Deny from all
Allow from 123.456.789.0
```

5. **Regular Backups**
- Backup database daily
- Backup uploaded files
- Keep multiple versions

6. **Update Passwords**
- Change admin passwords regularly
- Use strong passwords (12+ characters)

---

## 📊 Database Operations

### Add New Department

```sql
INSERT INTO departments (name, code, description, established_year) 
VALUES ('Mathematics', 'MATH', 'Department of Mathematics', 1960);
```

### Add New Admin User

```php
$password_hash = password_hash('new_password', PASSWORD_BCRYPT);
```

```sql
INSERT INTO admin_users (username, password_hash, full_name, email) 
VALUES ('newadmin', 'password_hash_here', 'Admin Name', 'email@example.com');
```

### View Activity Logs

```sql
SELECT a.*, u.username, u.full_name 
FROM admin_logs a 
JOIN admin_users u ON a.admin_id = u.id 
ORDER BY a.created_at DESC 
LIMIT 50;
```

---

## 🚦 Quick Start Checklist

- [ ] Upload all files to server
- [ ] Set folder permissions (777 for uploads/)
- [ ] Import database.sql
- [ ] Update config/config.php
- [ ] Test admin login
- [ ] Change default password
- [ ] Add your departments
- [ ] Add faculty members
- [ ] Test department page
- [ ] Integrate with main website
- [ ] Disable debug mode for production

---

## 📞 Support & Maintenance

### Regular Maintenance:
1. Review activity logs weekly
2. Backup database daily
3. Update PHP/MySQL when needed
4. Monitor upload folder size
5. Review and rotate log files

### Logs Location:
- Activity logs: `admin_logs` table
- Error logs: `/logs/error.log` (when debug disabled)

---

## 🎓 Code Examples

### Fetch Faculty Programmatically

```php
<?php
require_once 'includes/Faculty.php';
require_once 'includes/Department.php';

$facultyModel = new Faculty();
$departmentModel = new Department();

// Get department
$dept = $departmentModel->getByCode('ECON');

// Get faculty list
$faculty_list = $facultyModel->getByDepartment($dept['id']);

// Display
foreach ($faculty_list as $faculty) {
    echo $faculty['name'] . ' - ' . $faculty['designation'];
}
?>
```

### Custom Query

```php
<?php
require_once 'includes/Database.php';

$database = new Database();
$query = "SELECT * FROM faculty WHERE designation LIKE ? AND is_active = 1";
$results = $database->fetchAll($query, ['%Professor%']);
?>
```

---

## 📝 License & Credits

**Developed for:** BN College, Dhubri  
**Version:** 1.0  
**Date:** January 2026

**Security Note:** This system implements industry-standard security practices. However, always keep your software updated and follow security best practices.

---

**For additional help, refer to individual PHP file comments or contact your system administrator.**

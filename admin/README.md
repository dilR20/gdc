# Department Management System

A secure, modular PHP/MySQL system for managing college departments and faculty.

## 🚀 Quick Start

### 1. Upload Files
Upload all files to your web server

### 2. Create Database
```sql
CREATE DATABASE college_db;
```
Import `database.sql`

### 3. Configure
Edit `config/config.php`:
- Set database credentials
- Set site URL
- Configure upload paths

### 4. Set Permissions
```bash
chmod 777 uploads/faculty/
```

### 5. Login
Visit: `/admin/login.php`
- Username: `admin`
- Password: `admin123`

**⚠️ Change password immediately!**

## 📂 File Structure

```
department-system/
├── admin/              # Admin panel (separate design)
├── config/             # Configuration files
├── includes/           # PHP classes (modular)
├── css/                # Stylesheets
├── uploads/            # Uploaded files
├── department.php      # Frontend page
└── database.sql        # Database schema
```

## ✨ Features

- ✅ Secure admin panel
- ✅ CRUD faculty management
- ✅ Photo uploads with validation
- ✅ Department pages
- ✅ Results tracking
- ✅ Course management
- ✅ Activity logging
- ✅ CSRF protection
- ✅ SQL injection prevention
- ✅ Responsive design

## 🔒 Security

- Password hashing (BCRYPT)
- PDO prepared statements
- CSRF tokens
- File upload validation
- Session timeout
- Activity logging
- XSS prevention

## 📖 Documentation

See `SETUP_GUIDE.md` for complete documentation including:
- Installation guide
- Security features
- Customization guide
- Troubleshooting
- Code examples

## 🔧 Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- GD Library

## 📝 Default Credentials

**Admin Panel:**
- URL: `/admin/login.php`
- Username: `admin`
- Password: `admin123`

**Database:**
- Database: `college_db`
- Charset: `utf8mb4`

## 🎨 Customization

- Admin panel has completely separate design from main website
- Modular code structure for easy modifications
- All styles in separate CSS files
- Database operations in model classes

## 🌐 Frontend Integration

**Department Page URL:**
```
/department.php?code=ECON
```

**Add to Navigation:**
```html
<a href="department.php?code=ECON">Economics Department</a>
```

## 📊 Database Tables

- `departments` - Department info
- `faculty` - Faculty members
- `courses` - Course details
- `department_results` - Results data
- `admin_users` - Admin accounts
- `admin_logs` - Activity logs

## 🐛 Common Issues

**Photos not uploading?**
```bash
chmod 777 uploads/faculty/
```

**Database connection failed?**
- Check credentials in `config/config.php`
- Verify MySQL is running

**CSRF token error?**
- Clear browser cache
- Check session settings

## 🔐 Production Checklist

- [ ] Change default password
- [ ] Update database credentials
- [ ] Set DEBUG_MODE to false
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Configure regular backups
- [ ] Review security settings

## 📞 Support

For issues or questions:
1. Check `SETUP_GUIDE.md`
2. Review code comments
3. Check error logs
4. Contact system administrator

## 📄 License

Proprietary - BN College, Dhubri

---

**Version:** 1.0  
**Last Updated:** January 2026  
**Developed for:** Bhola Nath College, Dhubri

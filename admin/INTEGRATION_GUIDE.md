# Integrating Department System with Existing Website

This guide shows how to integrate the department management system with your existing BN College website.

## 🔗 Integration Steps

### 1. File Placement

**Option A: Same Directory (Recommended)**
```
yoursite.com/
├── index.html              (your existing homepage)
├── principal.html          (your existing pages)
├── config/
├── includes/
├── admin/
├── department.php          (new department page)
├── uploads/
└── css/
```

**Option B: Subdirectory**
```
yoursite.com/
├── index.html
├── departments/
│   ├── config/
│   ├── includes/
│   ├── admin/
│   ├── department.php
│   └── ...
```

### 2. Update Navigation Menu

#### In your existing navigation (e.g., in `components.js`):

```javascript
const navigationComponent = `
<div class="container-fluid">
    <nav class="navbar navbar-expand-lg">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="principal.html">Principal</a></li>
            
            <!-- Add Departments Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    Departments
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="department.php?code=ECON">Economics</a></li>
                    <li><a class="dropdown-item" href="department.php?code=CS">Computer Science</a></li>
                    <li><a class="dropdown-item" href="department.php?code=ENG">English</a></li>
                    <!-- Add more departments as needed -->
                </ul>
            </li>
            
            <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
        </ul>
    </nav>
</div>
`;
```

### 3. Include Common Header/Footer

Update `department.php` to use your existing header/footer:

**At the top of department.php (after opening `<body>` tag):**

```php
<body>
    <!-- Include your existing header -->
    <?php include 'includes/header.php'; // or your header file ?>
    
    <!-- OR use the component-based approach -->
    <header id="top-header"></header>
    <nav id="main-nav"></nav>
    
    <!-- Rest of department page code... -->
```

**At the bottom (before closing `</body>` tag):**

```php
    <!-- Include your existing footer -->
    <?php include 'includes/footer.php'; // or your footer file ?>
    
    <!-- OR use the component-based approach -->
    <footer id="main-footer"></footer>
    
    <!-- Load your existing components -->
    <script src="js/components.js"></script>
    <script>
        // Load header and footer
        document.getElementById('top-header').innerHTML = window.components.topHeader;
        document.getElementById('main-nav').innerHTML = window.components.navigation;
        document.getElementById('main-footer').innerHTML = window.components.footer;
    </script>
</body>
```

### 4. Match Styling

**Option A: Use Existing Styles**

In `department.php`, load your existing CSS:

```html
<head>
    <!-- Your existing styles -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modal.css">
    
    <!-- Department-specific styles -->
    <link rel="stylesheet" href="css/department.css">
</head>
```

**Option B: Update Department CSS**

Match colors in `css/department.css`:

```css
/* Use your existing color scheme */
.page-header {
    background: linear-gradient(135deg, #003366 0%, #0056b3 100%);
    /* Match your existing primary colors */
}

.faculty-card:hover {
    /* Match your existing hover effects */
}
```

### 5. Database Configuration

**Shared Database Option:**

If your existing site uses PHP/MySQL, share the database:

```php
// In config/config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_existing_db');  // Same as your main site
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_pass');
```

**Separate Database Option:**

Keep department system in separate database (`college_db`) for modularity.

### 6. Update Paths

If you place files in a subdirectory, update paths:

**In `department.php`:**

```php
require_once 'config/config.php';

// Update SITE_URL in config/config.php:
define('SITE_URL', 'http://yoursite.com/departments');
```

**In admin panel paths:**

```php
// Update image paths
<img src="../uploads/faculty/<?php echo $photo; ?>">
```

## 🎨 Styling Integration

### Match Department Page to Main Website

1. **Copy Variables from Main CSS:**

```css
/* From your main style.css */
:root {
    --primary-color: #003366;
    --secondary-color: #0056b3;
    --accent-color: #ffc107;
}
```

2. **Update Department CSS:**

Copy these variables to `css/department.css` and use them.

3. **Font Consistency:**

```css
/* Match fonts */
body {
    font-family: 'Your Font', sans-serif;
}
```

### Consistent Header/Footer

**Option 1: Server-Side Includes**

Create `includes/header.inc.php`:
```php
<header>
    <!-- Your header HTML -->
</header>
```

Include in all PHP pages:
```php
<?php include 'includes/header.inc.php'; ?>
```

**Option 2: JavaScript Components**

Already implemented in your existing site - just load the components script.

## 🔗 Linking Examples

### From Homepage to Department

```html
<!-- In your index.html -->
<div class="department-links">
    <a href="department.php?code=ECON" class="dept-card">
        <i class="fas fa-chart-line"></i>
        <h3>Economics</h3>
        <p>Explore our Economics department</p>
    </a>
</div>
```

### From Department to Principal

```html
<!-- In department.php -->
<a href="principal.html" class="btn btn-primary">
    Meet Our Principal
</a>
```

### Breadcrumb Integration

Update breadcrumb in `department.php`:

```php
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item"><a href="departments.html">Departments</a></li>
        <li class="breadcrumb-item active">
            <?php echo htmlspecialchars($department['name']); ?>
        </li>
    </ol>
</nav>
```

## 🔐 Admin Panel Integration

### Secure Admin Access

**Option 1: Separate Subdomain**
```
admin.yoursite.com -> points to /admin folder
```

**Option 2: Existing Path**
```
yoursite.com/admin/login.php
```

**Option 3: Custom Path**
Rename `/admin` to something less obvious:
```
yoursite.com/system-portal/login.php
```

### Add to Existing Admin Panel

If you have an existing admin system:

1. **Add links in your admin menu:**

```html
<li>
    <a href="/department-system/admin/index.php">
        Department Management
    </a>
</li>
```

2. **Shared Authentication:**

Modify `includes/Auth.php` to work with your existing auth system.

## 📊 Data Integration

### Share Faculty Data

If your main site needs faculty info:

```php
<?php
// In any page
require_once 'includes/Faculty.php';
require_once 'includes/Department.php';

$facultyModel = new Faculty();
$deptModel = new Department();

// Get Economics department faculty
$econ = $deptModel->getByCode('ECON');
$faculty = $facultyModel->getByDepartment($econ['id']);

// Display on homepage
foreach ($faculty as $member) {
    echo '<div class="faculty-highlight">';
    echo '<h4>' . $member['name'] . '</h4>';
    echo '<p>' . $member['designation'] . '</p>';
    echo '</div>';
}
?>
```

### API Endpoint (Optional)

Create `api/faculty.php`:

```php
<?php
header('Content-Type: application/json');
require_once '../includes/Faculty.php';

$deptCode = $_GET['dept'] ?? '';
// Fetch and return JSON
echo json_encode($faculty_list);
?>
```

Use in JavaScript:
```javascript
fetch('api/faculty.php?dept=ECON')
    .then(r => r.json())
    .then(data => {
        // Display faculty
    });
```

## 📱 Responsive Integration

Ensure department pages are responsive like main site:

```css
/* In department.css */
@media (max-width: 768px) {
    .faculty-card {
        /* Match mobile styles from main site */
    }
}
```

## ✅ Integration Checklist

- [ ] Files uploaded to correct location
- [ ] Database configured
- [ ] Navigation updated with department links
- [ ] Header/footer integrated
- [ ] Styles matched/coordinated
- [ ] Paths updated if using subdirectory
- [ ] Tested department pages
- [ ] Tested admin panel access
- [ ] Mobile responsiveness verified
- [ ] Links work in both directions
- [ ] Breadcrumbs functional
- [ ] Images loading correctly
- [ ] Search functionality (if applicable)
- [ ] Analytics tracking added

## 🐛 Common Integration Issues

### 1. Styles Not Matching

**Fix:** Ensure main CSS is loaded before department CSS:
```html
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/department.css">
```

### 2. Header/Footer Not Showing

**Fix:** Check file paths are correct:
```php
<?php include '../includes/header.php'; ?> // if in subdirectory
<?php include 'includes/header.php'; ?> // if in same directory
```

### 3. Links Broken

**Fix:** Use root-relative or absolute URLs:
```html
<a href="/department.php?code=ECON">Economics</a>
<!-- OR -->
<a href="https://yoursite.com/department.php?code=ECON">Economics</a>
```

### 4. Database Connection in Admin

**Fix:** Check path to config:
```php
require_once '../config/config.php'; // From admin folder
```

## 📞 Support

For integration issues:
1. Check file paths
2. Verify database config
3. Clear browser cache
4. Check error logs
5. Review console for JavaScript errors

---

**Ready to integrate?** Follow the steps above in order, testing after each step!

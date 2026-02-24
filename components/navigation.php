<style>
.main-navigation {
    background: #2563a8;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.main-navigation .container-fluid {
    padding: 0 30px;
}
.main-navigation .navbar {
    padding: 0;
}
.main-navigation .navbar-nav {
    width: 100%;
    display: flex;
    justify-content: space-between;
}
.main-navigation .nav-item {
    position: relative;
}
.main-navigation .nav-link {
    color: white !important;
    padding: 15px 18px;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    transition: all 0.3s;
    border: none;
    display: flex;
    align-items: center;
    gap: 5px;
}
.main-navigation .nav-link:hover,
.main-navigation .nav-link.active {
    background: #1e4d8b;
}
.main-navigation .dropdown-menu {
    background: #1e4d8b;
    border: none;
    border-radius: 0;
    margin: 0;
    padding: 0;
    min-width: 250px;
}
.main-navigation .dropdown-item {
    color: white;
    padding: 12px 20px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.3s;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.main-navigation .dropdown-item:hover {
    background: #174178;
    padding-left: 30px;
}
.main-navigation .dropdown-toggle::after {
    margin-left: 5px;
}
.navbar-toggler {
    border: 2px solid white;
    padding: 5px 10px;
}
.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}
@media (max-width: 992px) {
    .main-navigation .navbar-nav {
        background: #2563a8;
        padding: 10px 0;
    }
    .main-navigation .nav-link {
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .main-navigation .dropdown-menu {
        background: #1e4d8b;
    }
}


/* ── 2-Level Submenu ── */
.dropdown-submenu {
    position: relative;
}

.dropdown-submenu > .dropdown-menu.dropdown-submenu-menu {
    top: 0;
    left: 100%;
    margin-top: -1px;
    display: none;
    min-width: 220px;
    background: #174178;
}

.dropdown-submenu:hover > .dropdown-menu.dropdown-submenu-menu {
    display: block;
}

/* Arrow indicator on submenu parent */
.dropdown-submenu > .dropdown-item.dropdown-toggle::after {
    display: inline-block;
    margin-left: auto;
    float: right;
    margin-top: 6px;
    border-top: 4px solid transparent;
    border-bottom: 4px solid transparent;
    border-left: 4px solid white;
    border-right: none;
}

/* Mobile: show submenu on click instead of hover */
@media (max-width: 992px) {
    .dropdown-submenu > .dropdown-menu.dropdown-submenu-menu {
        left: 0;
        top: 100%;
        position: static;
        background: #122f5e;
    }
    .dropdown-submenu > .dropdown-item.dropdown-toggle::after {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid white;
        border-bottom: none;
        float: none;
        margin-left: 5px;
    }
}
</style>

<nav class="main-navigation">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home"></i> HOME
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            ABOUT US
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">History</a></li>
                            <li><a class="dropdown-item" href="#">Vision & Mission</a></li>
                            <li><a class="dropdown-item" href="#">Infrastructure</a></li>
                            <li><a class="dropdown-item" href="#">Campus Tour</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            ADMINISTRATION
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="principal-profile.php">Principal</a></li>
                            <li><a class="dropdown-item" href="#">Governing Body</a></li>
                            <li><a class="dropdown-item" href="#">Administrative Staff</a></li>
                        </ul>
                    </li>
                    <!-- REPLACE THIS SECTION IN YOUR NAVIGATION -->
                    
                    <!-- DEPARTMENTS - 2 Level Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            DEPARTMENTS
                        </a>
                        <ul class="dropdown-menu">
                            <!-- Arts & Humanities -->
                            <li class="dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle" href="#">
                                    <i class="fas fa-palette me-2"></i> Arts &amp; Humanities
                                </a>
                                <ul class="dropdown-menu dropdown-submenu-menu">
                                    <li><a class="dropdown-item" href="department.php?slug=economics">Economics</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=history">History</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=philosophy">Philosophy</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=nepali">Nepali</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=english">English</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=bodo">Bodo</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=political-science">Political Science</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=assamese">Assamese</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=geography">Geography</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=education">Education</a></li>
                                </ul>
                            </li>
                            <!-- Science -->
                            <li class="dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle" href="#">
                                    <i class="fas fa-flask me-2"></i> Science
                                </a>
                                <ul class="dropdown-menu dropdown-submenu-menu">
                                    <li><a class="dropdown-item" href="department.php?slug=physics">Physics</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=mathematics">Mathematics</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=chemistry">Chemistry</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=zoology">Zoology</a></li>
                                    <li><a class="dropdown-item" href="department.php?slug=botany">Botany</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            ADMISSION & COURSES
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Admission Notice</a></li>
                            <li><a class="dropdown-item" href="#">Course Details</a></li>
                            <li><a class="dropdown-item" href="#">Fee Structure</a></li>
                            <li><a class="dropdown-item" href="#">Eligibility</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LMS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LIBRARY</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">INTERNAL COMPLAIN</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            ACADEMICS
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Academic Calendar</a></li>
                            <li><a class="dropdown-item" href="#">Syllabus</a></li>
                            <li><a class="dropdown-item" href="#">Examination</a></li>
                            <li><a class="dropdown-item" href="#">Results</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">CONTACT</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</nav>

<script>
// Mobile: toggle submenu on click
document.querySelectorAll('.dropdown-submenu > .dropdown-toggle').forEach(function(el) {
    el.addEventListener('click', function(e) {
        if (window.innerWidth < 992) {
            e.preventDefault();
            e.stopPropagation();
            const sub = this.nextElementSibling;
            sub.style.display = sub.style.display === 'block' ? 'none' : 'block';
        }
    });
});
</script>
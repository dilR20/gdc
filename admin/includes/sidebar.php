<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
.sidebar-wrapper {
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    width: 260px;
    background: #2c3e50;
    transition: width 0.3s ease;
    z-index: 1000;
    overflow-y: auto;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
}

.sidebar-wrapper::-webkit-scrollbar {
    width: 6px;
}

.sidebar-wrapper::-webkit-scrollbar-track {
    background: #2c3e50;
}

.sidebar-wrapper::-webkit-scrollbar-thumb {
    background: #34495e;
    border-radius: 3px;
}

.sidebar-wrapper.collapsed {
    width: 70px;
}

.sidebar-header {
    padding: 20px;
    background: #34495e;
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 70px;
    flex-shrink: 0;
}

.sidebar-brand {
    color: white;
    font-size: 1.2rem;
    font-weight: bold;
    white-space: nowrap;
    opacity: 1;
    transition: opacity 0.3s;
}

.sidebar-wrapper.collapsed .sidebar-brand {
    opacity: 0;
}

.toggle-btn {
    background: #3498db;
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.toggle-btn:hover {
    background: #2980b9;
}

.expand-btn {
    position: fixed;
    top: 20px;
    left: 15px;
    background: #3498db;
    border: none;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 6px;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1001;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.expand-btn:hover {
    background: #2980b9;
}

.sidebar-menu {
    padding: 20px 0;
    list-style: none;
    margin: 0;
    flex: 1;
    overflow-y: auto;
}

.menu-item {
    margin: 5px 0;
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #bdc3c7;
    text-decoration: none;
    transition: all 0.3s;
}

.menu-link:hover {
    background: #34495e;
    color: white;
}

.menu-link.active {
    background: #3498db;
    color: white;
}

.menu-icon {
    width: 30px;
    min-width: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.menu-text {
    margin-left: 15px;
    white-space: nowrap;
    opacity: 1;
    transition: opacity 0.3s;
}

.sidebar-wrapper.collapsed .menu-text {
    opacity: 0;
    width: 0;
}

.section-title {
    padding: 15px 20px 5px;
    color: #95a5a6;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    white-space: nowrap;
    transition: all 0.3s;
}

.sidebar-wrapper.collapsed .section-title {
    opacity: 0;
    height: 0;
    padding: 0;
}

.dropdown-menu-custom {
    list-style: none;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    padding: 0;
    margin: 0;
}

.dropdown-menu-custom.show {
    max-height: 800px;
}

.dropdown-toggle-custom {
    display: flex;
    align-items: center;
}

.dropdown-icon {
    font-size: 12px;
    margin-left: auto;
    transition: transform 0.3s;
}

.sidebar-wrapper.collapsed .dropdown-icon {
    opacity: 0;
}

.dropdown-toggle-custom[aria-expanded="true"] .dropdown-icon {
    transform: rotate(180deg);
}

.submenu-link {
    padding-left: 65px;
    font-size: 14px;
}

.sidebar-wrapper.collapsed .dropdown-menu-custom {
    display: none;
}

.sidebar-footer {
    margin-top: auto;
    padding: 15px;
    background: #34495e;
    border-top: 1px solid #2c3e50;
    flex-shrink: 0;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #3498db;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}

.user-details {
    flex: 1;
    white-space: nowrap;
    transition: opacity 0.3s;
}

.sidebar-wrapper.collapsed .user-details {
    opacity: 0;
}

.user-name {
    font-size: 14px;
    font-weight: bold;
}

.user-email {
    font-size: 12px;
    color: #bdc3c7;
}

.sidebar-wrapper.collapsed .menu-link:hover::after {
    content: attr(data-title);
    position: absolute;
    left: 70px;
    top: 50%;
    transform: translateY(-50%);
    background: #34495e;
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    white-space: nowrap;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
</style>

<button class="expand-btn" id="expandBtn">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar-wrapper" id="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-brand">GyanPeeth</span>
        <button class="toggle-btn" id="toggleBtn">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-item">
            <a href="index.php" class="menu-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" data-title="Dashboard">
                <span class="menu-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>

        <div class="section-title">Announcements</div>
        
        <li class="menu-item">
            <a href="updates-list.php" class="menu-link" data-title="Latest Updates">
                <span class="menu-icon"><i class="fas fa-bell"></i></span>
                <span class="menu-text">Latest Updates</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="notifications-list.php" class="menu-link" data-title="Notifications">
                <span class="menu-icon"><i class="fas fa-bullhorn"></i></span>
                <span class="menu-text">Notifications</span>
            </a>
        </li>

        <div class="section-title">Content Management</div>

        <li class="menu-item">
            <a href="faculty-list.php" class="menu-link <?php echo strpos($current_page, 'faculty') !== false ? 'active' : ''; ?>" data-title="Faculty">
                <span class="menu-icon"><i class="fas fa-users"></i></span>
                <span class="menu-text">Faculty</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="departments-list.php" class="menu-link" data-title="Departments">
                <span class="menu-icon"><i class="fas fa-building"></i></span>
                <span class="menu-text">Departments</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="courses-list.php" class="menu-link" data-title="Courses">
                <span class="menu-icon"><i class="fas fa-book"></i></span>
                <span class="menu-text">Courses</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="results-list.php" class="menu-link" data-title="Results">
                <span class="menu-icon"><i class="fas fa-chart-line"></i></span>
                <span class="menu-text">Results</span>
            </a>
        </li>

        <div class="section-title">Administration</div>

        <li class="menu-item">
            <a href="principal-list.php" class="menu-link" data-title="Principal">
                <span class="menu-icon"><i class="fas fa-user-tie"></i></span>
                <span class="menu-text">Principal</span>
            </a>
        </li>

        <div class="section-title">IQAC</div>

        <li class="menu-item">
            <a href="#" class="menu-link dropdown-toggle-custom" onclick="toggleDropdown('iqacMenu')" data-title="IQAC">
                <span class="menu-icon"><i class="fas fa-award"></i></span>
                <span class="menu-text">IQAC Management</span>
                <i class="fas fa-chevron-down dropdown-icon"></i>
            </a>
            <ul class="dropdown-menu-custom" id="iqacMenu">
                <li><a href="iqac-notice.php" class="menu-link submenu-link">Notice</a></li>
                <li><a href="iqac-utility.php" class="menu-link submenu-link">Utility Format</a></li>
                <li><a href="iqac-aqar.php" class="menu-link submenu-link">AQAR</a></li>
                <li><a href="iqac-quest.php" class="menu-link submenu-link">Quest</a></li>
                <li><a href="iqac-academic.php" class="menu-link submenu-link">Academic Calendar</a></li>
                <li><a href="iqac-prospectus.php" class="menu-link submenu-link">Prospectus</a></li>
                <li><a href="iqac-activity.php" class="menu-link submenu-link">Activity</a></li>
                <li><a href="iqac-nirf.php" class="menu-link submenu-link">NIRF</a></li>
                <li><a href="iqac-minutes.php" class="menu-link submenu-link">Minutes</a></li>
                <li><a href="iqac-annual.php" class="menu-link submenu-link">Annual Reports</a></li>
                <li><a href="iqac-composition.php" class="menu-link submenu-link">Composition</a></li>
                <li><a href="iqac-accreditation.php" class="menu-link submenu-link">Accreditation</a></li>
            </ul>
        </li>

        <div class="section-title">Homepage</div>

        <li class="menu-item">
            <a href="#" class="menu-link dropdown-toggle-custom" onclick="toggleDropdown('homepageMenu')" data-title="Homepage">
                <span class="menu-icon"><i class="fas fa-home"></i></span>
                <span class="menu-text">Homepage Sections</span>
                <i class="fas fa-chevron-down dropdown-icon"></i>
            </a>
            <ul class="dropdown-menu-custom" id="homepageMenu">
                <li><a href="hero-sliders-list.php" class="menu-link submenu-link">Hero Sliders</a></li>
                <li><a href="homepage-items-list.php" class="menu-link submenu-link">Homepage Items</a></li>
                <li><a href="videos-list.php" class="menu-link submenu-link">Video Gallery</a></li>
            </ul>
        </li>

        <div class="section-title">Settings</div>

        <li class="menu-item">
            <a href="settings.php" class="menu-link" data-title="Settings">
                <span class="menu-icon"><i class="fas fa-cog"></i></span>
                <span class="menu-text">Settings</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="logout.php" class="menu-link" data-title="Logout">
                <span class="menu-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="menu-text">Logout</span>
            </a>
        </li>
    </ul>

    <?php if (isset($adminInfo)): ?>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?php echo strtoupper(substr($adminInfo['full_name'] ?? 'A', 0, 1)); ?></div>
            <div class="user-details">
                <div class="user-name"><?php echo htmlspecialchars($adminInfo['full_name'] ?? 'Admin'); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($adminInfo['email'] ?? ''); ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.querySelector('.admin-content');
    const expandBtn = document.getElementById('expandBtn');
    
    sidebar.classList.toggle('collapsed');
    
    if (sidebar.classList.contains('collapsed')) {
        content.style.marginLeft = '70px';
        expandBtn.style.display = 'flex';
    } else {
        content.style.marginLeft = '260px';
        expandBtn.style.display = 'none';
    }
}

function toggleDropdown(id) {
    event.preventDefault();
    const menu = document.getElementById(id);
    const toggle = event.currentTarget;
    
    menu.classList.toggle('show');
    toggle.setAttribute('aria-expanded', menu.classList.contains('show'));
}

document.getElementById('toggleBtn').onclick = toggleSidebar;
document.getElementById('expandBtn').onclick = toggleSidebar;
</script>
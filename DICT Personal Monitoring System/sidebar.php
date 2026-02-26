<?php
// This file contains the sidebar HTML that can be included in other pages
$current_page = basename($_SERVER['PHP_SELF']);

// Define user photo path
$default_avatar = 'assets/images/default-avatar.svg';
$user_photo = !empty($_SESSION['profile_photo']) ? 
    'uploads/profile_photos/' . $_SESSION['profile_photo'] . '?t=' . time() : 
    $default_avatar;

// Initialize counts with default values
$projects_count = 0;
$activities_count = [
    'pending' => 0,
    'in_progress' => 0,
    'completed' => 0,
    'total' => 0
];

try {
    // Include database configuration
    if (!function_exists('safeQuery')) {
        require_once __DIR__ . '/config/database.php';
    }
    
    // Get total projects count
    $projects_stmt = safeQuery($pdo, "SELECT COUNT(*) as count FROM projects");
    if ($projects_stmt) {
        $projects_data = $projects_stmt->fetch(PDO::FETCH_ASSOC);
        $projects_count = $projects_data ? (int)$projects_data['count'] : 0;
    }

    // Get activities count by status
    $activities_sql = "SELECT 
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'in progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        COUNT(*) as total
        FROM activities";
    
    $activities_stmt = safeQuery($pdo, $activities_sql);
    if ($activities_stmt) {
        $activities_data = $activities_stmt->fetch(PDO::FETCH_ASSOC);
        if ($activities_data) {
            $activities_count = [
                'pending' => (int)($activities_data['pending'] ?? 0),
                'in_progress' => (int)($activities_data['in_progress'] ?? 0),
                'completed' => (int)($activities_data['completed'] ?? 0),
                'total' => (int)($activities_data['total'] ?? 0)
            ];
        }
    }

} catch (Exception $e) {
    // Log the error but don't break the page
    error_log("Error in sidebar: " . $e->getMessage());
}
?>
<!-- Futuristic Smart Sidebar -->
<style>
    :root {
        --primary: #6c63ff;
        --secondary: #4cc9f0;
        --accent: #64ffda;
        --bg-dark: #0a192f;
        --bg-darker: #07122a;
        --text: #e6f1ff;
        --text-secondary: #94a3b8;
        --glass: rgba(100, 255, 218, 0.05);
        --glass-border: rgba(100, 255, 218, 0.1);
        --transition-base: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        --nav-item-padding: 0.5rem 1rem;
        --nav-icon-size: 1.1rem;
        --nav-text-size: 0.9rem;
    }
    
    /* Keyframes */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    
    @keyframes slideInLeft {
        from { transform: translateX(-20px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .sidebar {
        background: rgba(10, 25, 47, 0.85);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-right: 1px solid var(--glass-border);
        height: 100vh;
        width: 280px;
        z-index: 1000;
        left: 0;
        top: 0;
        box-shadow: 4px 0 30px rgba(0, 0, 0, 0.3);
        font-family: var(--font-primary);
        font-size: var(--nav-text-size);
        line-height: 1.5;
        color: var(--text);
        overflow: hidden;
        animation: slideInLeft 0.5s ease-out forwards;
        opacity: 0;
    }
    
    .nav-item {
        display: flex;
        align-items: center;
        padding: var(--nav-item-padding);
        color: var(--text);
        text-decoration: none;
        border-radius: 8px;
        margin-bottom: 4px;
        transition: var(--transition-base);
        font-size: var(--nav-text-size);
        font-weight: 500;
    }
    
    .nav-item i {
        font-size: var(--nav-icon-size);
        margin-right: 0.75rem;
        width: 20px;
        text-align: center;
    }
    
    .nav-item:hover {
        background: rgba(100, 255, 218, 0.1);
        transform: translateX(5px);
    }
    
    .nav-item.active {
        background: rgba(100, 255, 218, 0.1);
        color: var(--accent);
        font-weight: 600;
    }
    
    .nav-item.active i {
        color: var(--accent);
    }
    
    .badge {
        font-size: 0.65rem;
        font-weight: 600;
        padding: 0.2em 0.6em;
        border-radius: 10px;
        margin-left: auto;
    }
    
    .sidebar-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        min-width: 100px;
        background: transparent;
        border: 1px solid rgba(var(--btn-color-rgb), 0.2);
        color: var(--btn-color, var(--accent));
        backdrop-filter: blur(5px);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-btn i {
        font-size: 1rem;
        margin-right: 0.5rem;
    }
    
    .sidebar-btn:hover {
        background: rgba(var(--btn-color-rgb), 0.1);
        transform: translateY(-2px);
        text-decoration: none;
    }
    
    .sidebar-btn:active {
        transform: translateY(0);
    }
</style>

<div class="sidebar position-fixed d-flex flex-column transition-all">
    <!-- App Header with Holographic Effect -->
    <div class="sidebar-header text-center py-3 px-3 position-relative" style="
        background: transparent;
        border-bottom: 1px solid var(--glass-border);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    ">
        <div class="d-flex align-items-center justify-content-center" style="position: relative;">
            <h4 class="mb-0" style="
                font-weight: 800;
                letter-spacing: 1px;
                font-size: 1.4rem;
                text-transform: uppercase;
                background: linear-gradient(90deg, var(--primary), var(--secondary));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin: 0;
                padding: 0;
                line-height: 1.2;
                text-shadow: 0 2px 10px rgba(76, 201, 240, 0.15);
                transition: all 0.3s ease;
            ">
                DICT PMS
            </h4>
        </div>
    </div>

    <!-- User Profile with Holographic Card -->
    <div class="user-info-section text-center py-4 px-3 position-relative" style="
        background: linear-gradient(145deg, rgba(76, 201, 240, 0.05), rgba(100, 255, 218, 0.05));
        border-bottom: 1px solid var(--glass-border);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        margin: 10px 15px;
        border-radius: 12px;
        box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.1);
    ">
        <!-- Animated background elements -->
        <div class="position-absolute" style="
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 30%, rgba(100, 255, 218, 0.05) 0%, transparent 70%);
            top: 0;
            left: 0;
            z-index: 0;
        "></div>
        
       <div class="user-avatar position-relative" style="
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background-image: url('<?php echo $user_photo; ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px -5px rgba(108, 99, 255, 0.4);
            z-index: 1;
            animation: float 6s ease-in-out infinite;
            overflow: hidden;
        ">
            <?php if (empty($user_photo) || strpos($user_photo, 'default-avatar') !== false): ?>
                <i class="bi bi-person" style="font-size: 2rem; color: white; background: linear-gradient(145deg, var(--primary), var(--secondary)); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"></i>
            <?php endif; ?>
            <div class="online-indicator" style="
                position: absolute;
                bottom: 5px;
                right: 5px;
                width: 12px;
                height: 12px;
                background: #4cff8f;
                border-radius: 50%;
                border: 2px solid var(--bg-darker);
                box-shadow: 0 0 10px #4cff8f;
            "></div>
        </div>
        
        <h5 class="welcome-text mb-2 position-relative" style="
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 6px 0;
            transition: all 0.3s ease;
            display: inline-block;
            line-height: 1.3;
            background: linear-gradient(90deg, var(--text), #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 10px rgba(255, 255, 255, 0.1);
        ">
            <?php echo htmlspecialchars(explode(' ', $_SESSION['full_name'])[0]); ?>
            <i class="bi bi-patch-check-fill ms-1" style="color: var(--accent); font-size: 1rem; vertical-align: middle;"></i>
        </h5>
        
        <p class="user-role position-relative" style="
            font-size: 0.7rem;
            color: var(--accent);
            background: linear-gradient(90deg, rgba(76, 201, 240, 0.1), rgba(100, 255, 218, 0.1));
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin: 6px 0 0;
            transition: all 0.3s ease;
            border: 1px solid var(--glass-border);
            line-height: 1.4;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 2px 10px -5px rgba(100, 255, 218, 0.2);
        ">
            <i class="bi bi-stars me-1"></i> Administrator
        </p>
        
        <!-- Interactive Hover Effect -->
        <div class="hover-bg" style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%), 
                rgba(100, 255, 218, 0.1) 0%, 
                transparent 70%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
            z-index: 0;
        "></div>
        
        <!-- Glow effect -->
        <div class="glow" style="
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: radial-gradient(circle at 50% 0%, 
                rgba(108, 99, 255, 0.1) 0%, 
                transparent 70%);
            z-index: -1;
            opacity: 0.5;
        "></div>
    </div>

<script>
// Enhanced interactive effects for sidebar
document.addEventListener('DOMContentLoaded', function() {
    // User card interaction
    const userInfoSection = document.querySelector('.user-info-section');
    const hoverBg = document.querySelector('.hover-bg');
    const navItems = document.querySelectorAll('.nav-item');
    
    // Add interactive hover effect to user info section
    if (userInfoSection && hoverBg) {
        userInfoSection.addEventListener('mousemove', (e) => {
            const rect = userInfoSection.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Update CSS variables for dynamic background effect
            userInfoSection.style.setProperty('--mouse-x', `${x}px`);
            userInfoSection.style.setProperty('--mouse-y', `${y}px`);
            
            hoverBg.style.opacity = '1';
            
            // Add subtle tilt effect
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const angleX = (y - centerY) / 20;
            const angleY = (centerX - x) / 20;
            
            userInfoSection.style.transform = `perspective(1000px) rotateX(${angleX}deg) rotateY(${angleY}deg)`;
        });
        
        userInfoSection.addEventListener('mouseleave', () => {
            hoverBg.style.opacity = '0';
            userInfoSection.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)';
        });
    }
    
    // Enhanced hover effect for nav items
    navItems.forEach(item => {
        item.addEventListener('mouseenter', (e) => {
            item.style.transform = 'translateX(8px) scale(1.02)';
            item.style.background = 'rgba(100, 255, 218, 0.08)';
            item.style.boxShadow = '0 4px 15px -5px rgba(100, 255, 218, 0.2)';
            
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.pointerEvents = 'none';
            
            const rect = item.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${e.clientX - rect.left - size/2}px`;
            ripple.style.top = `${e.clientY - rect.top - size/2}px`;
            
            item.style.position = 'relative';
            item.style.overflow = 'hidden';
            item.appendChild(ripple);
            
            // Remove ripple after animation
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
        
        item.addEventListener('mouseleave', () => {
            item.style.transform = 'translateX(0) scale(1)';
            item.style.background = '';
            item.style.boxShadow = '';
        });
    });
    
    // Add active state to current page
    const currentPath = window.location.pathname.split('/').pop() || 'dashboard.php';
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href)) {
            link.closest('.nav-item').classList.add('active');
            link.style.background = 'rgba(100, 255, 218, 0.1)';
            link.style.borderLeft = '3px solid #64ffda';
        }
    });
});

// Function to collapse all sections
function collapseAllSections() {
    document.querySelectorAll('.nav-section .collapse').forEach(el => {
        if (el.classList.contains('show')) {
            const sectionId = el.id.replace('-collapse', '');
            const sectionIcon = document.querySelector(`[data-bs-target="#${el.id}"] .bi`);
            if (sectionIcon) {
                sectionIcon.classList.remove('bi-chevron-down');
                sectionIcon.classList.add('bi-chevron-right');
            }
            el.classList.remove('show');
            localStorage.removeItem(`sidebar-${sectionId}-expanded`);
        }
    });
}

// Toggle collapsible sections
function toggleCollapse(section, isLinkClick = false) {
    const collapseElement = document.getElementById(section + '-collapse');
    const icon = document.querySelector(`[data-bs-target="#${section}-collapse"] .bi`);
    
    if (!collapseElement) return;
    
    // If this is a link click, just expand the section without toggling
    if (isLinkClick) {
        // Collapse all sections first
        collapseAllSections();
        // Then expand the clicked section
        collapseElement.classList.add('show');
        if (icon) {
            icon.classList.remove('bi-chevron-right');
            icon.classList.add('bi-chevron-down');
        }
        localStorage.setItem(`sidebar-${section}-expanded`, 'true');
        return;
    }
    
    // For section header clicks, toggle the section
    if (collapseElement.classList.contains('show')) {
        // Collapse the section
        collapseElement.classList.remove('show');
        if (icon) {
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-right');
        }
        localStorage.removeItem(`sidebar-${section}-expanded`);
    } else {
        // Collapse all sections first
        collapseAllSections();
        // Then expand the clicked section
        collapseElement.classList.add('show');
        if (icon) {
            icon.classList.remove('bi-chevron-right');
            icon.classList.add('bi-chevron-down');
        }
        localStorage.setItem(`sidebar-${section}-expanded`, 'true');
    }
}

// Initialize sidebar state on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize collapsible sections
    ['performance', 'system'].forEach(section => {
        const isExpanded = localStorage.getItem(`sidebar-${section}-expanded`) === 'true';
        const collapseElement = document.getElementById(`${section}-collapse`);
        const icon = document.querySelector(`[data-bs-target="#${section}-collapse"] .bi`);
        
        if (collapseElement) {
            // Start collapsed by default, unless explicitly expanded or contains active item
            if (isExpanded || collapseElement.querySelector('.active')) {
                collapseElement.classList.add('show');
                if (icon) {
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-down');
                }
            } else {
                collapseElement.classList.remove('show');
                if (icon) {
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-right');
                }
            }
        }
    });
});
</script>

    <!-- Navigation Menu -->
    <div class="sidebar-nav flex-grow-1 overflow-auto py-3 transition-all" style="
        scrollbar-width: thin;
        padding: 0 10px;
        animation: fadeIn 0.5s ease-out 0.2s forwards;
        opacity: 0;
    ">
        <div class="nav-menu py-2">
            <a href="dashboard.php" class="nav-item d-flex align-items-center px-3 py-2 rounded-3 mb-1 <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>" 
   style="
       transition: all 0.25s ease;
       color: #e2e8f0;
       text-decoration: none;
       margin-bottom: 4px;
       font-size: 0.9rem;
       font-weight: 500;
       letter-spacing: 0.2px;
   "
   onmouseover="this.style.background='rgba(100, 255, 218, 0.1)'; this.style.transform='translateX(5px)';"
   onmouseout="if(!this.classList.contains('active')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }"
   onclick="collapseAllSections();">
                <i class="bi bi-speedometer2 me-2" style="font-size: 1rem;"></i>
                <span>Dashboard</span>
            </a>
            <a href="notes.php" class="nav-item d-flex align-items-center px-3 py-2 rounded-3 mb-1 <?php echo $current_page === 'notes.php' ? 'active' : ''; ?>" 
               style="
                   transition: all 0.25s ease;
                   color: #e2e8f0;
                   text-decoration: none;
                   margin-bottom: 4px;
                   font-size: 0.9rem;
                   font-weight: 500;
                   letter-spacing: 0.2px;
               "
               onmouseover="this.style.background='rgba(100, 255, 218, 0.1)'; this.style.transform='translateX(5px)';"
               onmouseout="if(!this.classList.contains('active')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }"
               onclick="collapseAllSections();">
                <i class="bi bi-journal-check me-2" style="font-size: 1rem;"></i>
                <span>Notes, Checklist and Reminder</span>
            </a>
            <a href="projects.php" class="nav-item d-flex align-items-center px-3 py-2 rounded-3 mb-1 <?php echo $current_page === 'projects.php' ? 'active' : ''; ?>" 
               style="
                   transition: all 0.25s ease;
                   color: #e2e8f0;
                   text-decoration: none;
                   margin-bottom: 4px;
                   font-size: 0.9rem;
                   font-weight: 500;
                   letter-spacing: 0.2px;
               "
               onmouseover="this.style.background='rgba(100, 255, 218, 0.1)'; this.style.transform='translateX(5px)';"
               onmouseout="if(!this.classList.contains('active')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }"
               onclick="collapseAllSections();">
                <i class="bi bi-folder me-2" style="font-size: 1rem;"></i>
                <span>Projects</span>
                <span class="badge bg-danger ms-auto" style="background: #ff4757 !important; font-size: 0.65rem; padding: 0.2em 0.6em; font-weight: 600; min-width: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px;"><?php echo $projects_count; ?></span>
            </a>
            <a href="activities.php" class="nav-item d-flex align-items-center px-3 py-2 rounded-3 mb-1 <?php echo $current_page === 'activities.php' ? 'active' : ''; ?>"
               style="
                   transition: all 0.25s ease;
                   color: #e2e8f0;
                   text-decoration: none;
                   margin-bottom: 4px;
                   font-size: 0.9rem;
                   font-weight: 500;
                   letter-spacing: 0.2px;
               "
               onmouseover="this.style.background='rgba(100, 255, 218, 0.1)'; this.style.transform='translateX(5px)';"
               onmouseout="if(!this.classList.contains('active')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }"
               data-bs-toggle="tooltip" 
               data-bs-placement="right" 
               title="Pending: <?php echo $activities_count['pending'] ?? 0; ?> • In Progress: <?php echo $activities_count['in_progress'] ?? 0; ?> • Completed: <?php echo $activities_count['completed'] ?? 0; ?>"
               onclick="collapseAllSections();">
                <i class="bi bi-list-check me-2" style="font-size: 1rem;"></i>
                <span>Activities</span>
                <span class="badge ms-auto" style="background: #ffbe0b; color: #1a1a2e; font-size: 0.65rem; padding: 0.2em 0.6em; font-weight: 600; min-width: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px;">
                    <?php echo $activities_count['total'] ?? 0; ?>
                </span>
            </a>
            <a href="point_of_contacts.php" class="nav-item d-flex align-items-center px-3 py-2 rounded-3 mb-1 <?php echo $current_page === 'point_of_contacts.php' ? 'active' : ''; ?>"
               style="
                   transition: all 0.25s ease;
                   color: #e2e8f0;
                   text-decoration: none;
                   margin-bottom: 4px;
                   font-size: 0.9rem;
                   font-weight: 500;
                   letter-spacing: 0.2px;
               "
               onmouseover="this.style.background='rgba(100, 255, 218, 0.1)'; this.style.transform='translateX(5px)';"
               onmouseout="if(!this.classList.contains('active')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }"
               onclick="collapseAllSections();">
                <i class="bi bi-people me-2" style="font-size: 1rem;"></i>
                <span>Point of Contacts</span>
            </a>
            
            <!-- Performance Section -->
            <div class="nav-section transition-all">
                <div class="nav-section-title px-3 py-2 mt-2 mb-1 d-flex align-items-center transition-colors hover:bg-opacity-10 hover:bg-white rounded" data-bs-toggle="collapse" data-bs-target="#performance-collapse" aria-expanded="false" aria-controls="performance-collapse" onclick="toggleCollapse('performance')" style="cursor: pointer;">
                    <span style="font-size: 0.75rem; color: var(--accent-color); font-weight: 600; letter-spacing: 1px;">PERFORMANCE</span>
                    <div class="flex-grow-1 ms-2" style="height: 1px; background: linear-gradient(to right, rgba(100, 255, 218, 0.3), transparent);"></div>
                    <i class="bi bi-chevron-down ms-2" style="font-size: 0.7rem; color: var(--accent-color);"></i>
                </div>
                
                <div class="collapse" id="performance-collapse">
                    <div class="nav-items">
                        <a href="offset_status.php" class="nav-item d-flex align-items-center px-3 py-2 <?php echo in_array($current_page, ['offset_status.php']) ? 'active' : ''; ?>">
                            <i class="bi bi-clock-history me-2"></i>
                            <span>Offset Status</span>
                        </a>
                        <a href="ipcr_reports.php" class="nav-item d-flex align-items-center px-3 py-2 <?php echo in_array($current_page, ['ipcr_reports.php']) ? 'active' : ''; ?>">
                            <i class="bi bi-file-earmark-bar-graph me-2"></i>
                            <span>IPCR Reports</span>
                        </a>
                        <a href="tev_claims.php" class="nav-item d-flex align-items-center px-3 py-2 <?php echo in_array($current_page, ['tev_claims.php']) ? 'active' : ''; ?>">
                            <i class="bi bi-cash-coin me-2"></i>
                            <span>TEV Claims Status</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- System Section -->
            <div class="nav-section">
                <div class="nav-section-title px-3 py-2 mt-3 mb-1 d-flex align-items-center" data-bs-toggle="collapse" data-bs-target="#system-collapse" aria-expanded="false" aria-controls="system-collapse" onclick="toggleCollapse('system')" style="cursor: pointer;">
                    <span style="font-size: 0.75rem; color: var(--accent-color); font-weight: 600; letter-spacing: 1px;">SYSTEM</span>
                    <div class="flex-grow-1 ms-2" style="height: 1px; background: linear-gradient(to right, rgba(100, 255, 218, 0.3), transparent);"></div>
                    <i class="bi bi-chevron-down ms-2" style="font-size: 0.7rem; color: var(--accent-color);"></i>
                </div>
                
                <div class="collapse" id="system-collapse">
                    <div class="nav-items">
                        <a href="settings.php" class="nav-item d-flex align-items-center px-3 py-2 <?php echo in_array($current_page, ['settings.php']) ? 'active' : ''; ?>">
                            <i class="bi bi-gear me-2"></i>
                            <span>Settings</span>
                        </a>
                        <a href="about.php" class="nav-item d-flex align-items-center px-3 py-2 <?php echo in_array($current_page, ['about.php']) ? 'active' : ''; ?>">
                            <i class="bi bi-info-circle me-2"></i>
                            <span>About</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="sidebar-footer p-3" style="
        background: transparent;
        border-top: 1px solid rgba(100, 255, 218, 0.1);
        margin-top: auto;
    ">
        <div class="d-flex justify-content-between align-items-center">
            <a href="profile.php" class="sidebar-btn" style="--btn-color: var(--accent); --btn-color-rgb: 100, 255, 218;">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </a>
            <a href="logout.php" class="sidebar-btn" style="--btn-color: #ff6b81; --btn-color-rgb: 255, 107, 129;">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>

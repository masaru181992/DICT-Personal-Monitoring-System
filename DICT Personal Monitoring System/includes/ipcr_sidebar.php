<?php
// This file contains the IPCR sidebar HTML that can be included in other pages
require_once __DIR__ . '/../config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Get counts for sidebar
$dashboard_count = 0; // Dashboard doesn't need a count

// Get total projects count
$projects_count = $pdo->query("SELECT COUNT(*) as count FROM projects")->fetch(PDO::FETCH_ASSOC)['count'];

// Get activities count by status
$activities_count = $pdo->query("SELECT 
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'in progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    COUNT(*) as total
    FROM activities")->fetch(PDO::FETCH_ASSOC);
?>
<!-- IPCR Sidebar -->
<div class="sidebar position-fixed d-flex flex-column" style="
    background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
    border-right: 1px solid rgba(100, 255, 218, 0.1);
    height: 100vh;
    width: 280px;
    z-index: 1000;
    left: 0;
    top: 0;
    transition: all 0.3s ease-in-out;
    box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2);
    font-family: 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', sans-serif;
    font-size: 14px;
    line-height: 1.5;
    color: #e2e8f0;
">
    <!-- App Header with Animation -->
    <div class="sidebar-header text-center py-4 px-3" style="
        background: rgba(26, 26, 46, 0.7);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(100, 255, 218, 0.1);
        transition: all 0.3s ease;
    ">
        <div class="d-flex align-items-center justify-content-center mb-2">
            <i class="bi bi-speedometer2 me-2" style="color: #64ffda; font-size: 1.8rem;"></i>
            <h4 class="mb-0" style="
                font-weight: 700;
                letter-spacing: 0.5px;
                font-size: 1.3rem;
                text-transform: uppercase;
                background: linear-gradient(90deg, #64ffda, #4cc9f0);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                transition: all 0.3s ease;
                margin: 0;
                padding: 0;
                line-height: 1.2;
            ">
                DICT PMS
            </h4>
        </div>
    </div>

    <!-- User Info with Hover Effect -->
    <div class="user-info-section text-center py-4 px-3" style="
        background: rgba(100, 255, 218, 0.03);
        border-bottom: 1px solid rgba(100, 255, 218, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    ">
        <div class="user-avatar" style="
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: rgba(100, 255, 218, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #64ffda;
            transition: all 0.3s ease;
        ">
            <i class="bi bi-person" style="font-size: 2.5rem; color: #64ffda;"></i>
        </div>
        
        <h5 class="welcome-text mb-2" style="
            font-size: 1.25rem;
            font-weight: 600;
            color: #ffffff;
            margin: 0 0 4px 0;
            transition: all 0.3s ease;
            position: relative;
            display: inline-block;
            line-height: 1.3;
        ">
            <?php echo htmlspecialchars(explode(' ', $_SESSION['full_name'])[0]); ?>
            <i class="bi bi-patch-check-fill ms-1" style="color: #4cc9f0; font-size: 1.2rem; vertical-align: middle;"></i>
        </h5>
        
        <p class="user-role" style="
            font-size: 0.75rem;
            color: #64ffda;
            background: rgba(100, 255, 218, 0.1);
            display: inline-block;
            padding: 3px 14px;
            border-radius: 20px;
            font-weight: 500;
            margin: 4px 0 0;
            transition: all 0.3s ease;
            border: 1px solid rgba(100, 255, 218, 0.2);
            line-height: 1.4;
            letter-spacing: 0.3px;
        ">
            <?php echo htmlspecialchars(ucfirst($_SESSION['role'] ?? 'User')); ?>
        </p>
        <div class="current-time d-flex flex-column align-items-center small mt-2" style="color: #a0aec0; line-height: 1.2;">
            <div class="date-display mb-1">
                <i class="bi bi-calendar3 me-1"></i>
                <span id="current-date"><?php echo date('F j, Y'); ?></span>
            </div>
            <div class="time-display">
                <i class="bi bi-clock me-1"></i>
                <span id="current-time"><?php echo date('g:i:s A'); ?></span>
            </div>
        </div>
    </div>

    <script>
    // Update time every second
    function updateLiveDateTime() {
        const now = new Date();
        
        // Update date (only if it's a new day)
        const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions);
        
        // Update time with seconds
        const timeOptions = { 
            hour: 'numeric', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: true 
        };
        document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions);
    }

    // Update time immediately and then every second
    updateLiveDateTime();
    setInterval(updateLiveDateTime, 1000);
    </script>

    <!-- Navigation Menu -->
    <div class="flex-grow-1 overflow-auto" style="scrollbar-width: thin;">
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
               onmouseout="if(!this.classList.contains('active')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }">
                <i class="bi bi-folder me-2" style="font-size: 1rem;"></i>
                <span>Projects</span>
                <span class="badge ms-auto" style="background: #ff4757; color: #ffffff; font-size: 0.65rem; padding: 0.2em 0.6em; font-weight: 600; min-width: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px;">
                    <?php echo $projects_count; ?>
                </span>
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
               title="Pending: <?php echo $activities_count['pending'] ?? 0; ?> • In Progress: <?php echo $activities_count['in_progress'] ?? 0; ?> • Completed: <?php echo $activities_count['completed'] ?? 0; ?>">
                <i class="bi bi-list-check me-2" style="font-size: 1rem;"></i>
                <span>Activities</span>
                <span class="badge ms-auto" style="background: #ffbe0b; color: #1a1a2e; font-size: 0.65rem; padding: 0.2em 0.6em; font-weight: 600; min-width: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px;">
                    <?php echo $activities_count['total'] ?? 0; ?>
                </span>
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
               onmouseout="if(!this.classList.contains('active')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }">
                <i class="bi bi-journal-text me-2" style="font-size: 1rem;"></i>
                <span>Notes</span>
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
               onmouseout="if(!this.classList.contains('active')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }">
                <i class="bi bi-person-lines-fill me-2" style="font-size: 1rem;"></i>
                <span>Point of Contacts</span>
            </a>
            
            <!-- IPCR Section -->
            <div class="nav-section-title px-3 py-2 mt-2 mb-1 d-flex align-items-center">
                <span style="font-size: 0.75rem; color: var(--accent-color); font-weight: 600; letter-spacing: 1px;">PERFORMANCE</span>
                <div class="flex-grow-1 ms-2" style="height: 1px; background: linear-gradient(to right, rgba(100, 255, 218, 0.3), transparent);"></div>
            </div>
            
            <a href="add_ipcr_target.php" class="nav-item d-flex align-items-center px-3 py-2 rounded-3 mb-1 <?php echo $current_page === 'add_ipcr_target.php' ? 'active' : ''; ?>"
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
               onmouseout="if(!this.classList.contains('active')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }">
                <i class="bi bi-plus-circle me-2" style="font-size: 1rem;"></i>
                <span>Add IPCR Target</span>
            </a>
            <a href="ipcr_reports.php" class="nav-item d-flex align-items-center px-3 py-2 rounded-3 mb-1 <?php echo $current_page === 'ipcr_reports.php' ? 'active' : ''; ?>"
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
               onmouseout="if(!this.classList.contains('active')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }">
                <i class="bi bi-file-earmark-text me-2" style="font-size: 1rem;"></i>
                <span>IPCR Reports</span>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div class="sidebar-footer p-3" style="
        background: rgba(26, 26, 46, 0.7);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(100, 255, 218, 0.1);
        margin-top: auto;
    ">
        <div class="d-flex justify-content-between align-items-center">
            <a href="profile.php" class="btn btn-sm" style="
                background: transparent;
                border: 1px solid rgba(100, 255, 218, 0.3);
                color: #64ffda;
                border-radius: 6px;
                padding: 6px 12px;
                font-size: 0.85rem;
                font-weight: 500;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 100px;
                text-decoration: none;
                font-family: inherit;
            ">
                <i class="bi bi-person me-2"></i> Profile
            </a>
            <a href="logout.php" class="btn btn-sm" style="
                background: rgba(255, 71, 87, 0.1);
                border: 1px solid rgba(255, 71, 87, 0.3);
                color: #ff6b81;
                border-radius: 6px;
                padding: 6px 12px;
                font-size: 0.85rem;
                font-weight: 500;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 100px;
                text-decoration: none;
                font-family: inherit;
            ">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>
    </div>
</div>

<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl, {
        trigger: 'hover',
        placement: 'right',
        container: 'body',
        customClass: 'custom-tooltip',
        html: true
    });
});

// Add active state to current page link
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop() || 'dashboard.php';
    const navLinks = document.querySelectorAll('.nav-item');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage.includes('ipcr') && href.includes('ipcr'))) {
            link.classList.add('active');
            link.style.background = 'rgba(100, 255, 218, 0.15)';
            link.style.borderLeft = '3px solid #64ffda';
            link.style.transform = 'translateX(5px)';
        }
        
        // Add click effect
        link.addEventListener('click', function() {
            navLinks.forEach(l => {
                l.classList.remove('active');
                l.style.background = 'transparent';
                l.style.borderLeft = 'none';
            });
            
            this.classList.add('active');
            this.style.background = 'rgba(100, 255, 218, 0.15)';
            this.style.borderLeft = '3px solid #64ffda';
        });
    });
    
    // Add hover effect to user avatar
    const userAvatar = document.querySelector('.user-avatar');
    if (userAvatar) {
        userAvatar.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 0 20px rgba(100, 255, 218, 0.3)';
        });
        
        userAvatar.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    }
});

// Smooth scroll for sidebar
const sidebar = document.querySelector('.sidebar');
if (sidebar) {
    sidebar.addEventListener('wheel', function(e) {
        if (this.scrollHeight > this.clientHeight) {
            e.preventDefault();
            this.scrollTop += e.deltaY;
        }
    }, { passive: false });
}
</script>

<style>
/* Custom scrollbar for sidebar */
.sidebar::-webkit-scrollbar {
    width: 5px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(100, 255, 218, 0.05);
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(100, 255, 218, 0.2);
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(100, 255, 218, 0.3);
}

/* Custom tooltip styling */
.custom-tooltip .tooltip-inner {
    background-color: #1a1a2e;
    color: #e2e8f0;
    border: 1px solid rgba(100, 255, 218, 0.2);
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 0.8rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.custom-tooltip.bs-tooltip-end .tooltip-arrow::before {
    border-right-color: #1a1a2e;
}

/* Active state for nav items */
.nav-item.active {
    background: rgba(100, 255, 218, 0.15) !important;
    border-left: 3px solid #64ffda !important;
    color: #ffffff !important;
    font-weight: 500 !important;
}

/* Hover effect for buttons in footer */
.sidebar-footer a {
    transition: all 0.3s ease !important;
}

.sidebar-footer a:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

/* Animation for sidebar elements */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Apply animation to nav items with delay */
.nav-item {
    animation: fadeInUp 0.4s ease-out forwards;
    opacity: 0;
}

.nav-item:nth-child(1) { animation-delay: 0.1s; }
.nav-item:nth-child(2) { animation-delay: 0.15s; }
.nav-item:nth-child(3) { animation-delay: 0.2s; }
.nav-item:nth-child(4) { animation-delay: 0.25s; }
.nav-item:nth-child(5) { animation-delay: 0.3s; }
.nav-item:nth-child(6) { animation-delay: 0.35s; }
.nav-item:nth-child(7) { animation-delay: 0.4s; }
.nav-item:nth-child(8) { animation-delay: 0.45s; }
</style>

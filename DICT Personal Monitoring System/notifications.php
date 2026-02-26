<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle notification actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'mark_as_read' && isset($_POST['notification_id'])) {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$_POST['notification_id'], $_SESSION['user_id']])) {
            $success_message = "Notification marked as read!";
        } else {
            $error_message = "Error updating notification status.";
        }
    } elseif ($_POST['action'] == 'delete' && isset($_POST['notification_id'])) {
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$_POST['notification_id'], $_SESSION['user_id']])) {
            $success_message = "Notification deleted successfully!";
        } else {
            $error_message = "Error deleting notification.";
        }
    } elseif ($_POST['action'] == 'mark_all_read') {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        if ($stmt->execute([$_SESSION['user_id']])) {
            $success_message = "All notifications marked as read!";
        } else {
            $error_message = "Error updating notifications.";
        }
    } elseif ($_POST['action'] == 'clear_all') {
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE user_id = ?");
        if ($stmt->execute([$_SESSION['user_id']])) {
            $success_message = "All notifications cleared!";
        } else {
            $error_message = "Error clearing notifications.";
        }
    }
}

// Fetch all notifications for the current user
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();

// Get unread count
$unread_count = $pdo->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = " . $_SESSION['user_id'] . " AND is_read = 0")->fetch()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Personal Monitoring System - Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary-bg: #0f172a;
            --secondary-bg: #1e293b;
            --accent-color: #64ffda;
            --accent-secondary: #7c3aed;
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
        }

        body {
            background-color: #0f172a;
            color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .main-content {
            margin-left: 350px;
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s;
        }

        .card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            backdrop-filter: blur(10px);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: rgba(30, 41, 59, 0.8);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .notification-item {
            border-left: 3px solid var(--accent-color);
            padding: 1rem;
            margin-bottom: 1rem;
            background: rgba(30, 41, 59, 0.5);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .notification-item:hover {
            transform: translateX(5px);
            background: rgba(30, 41, 59, 0.8);
        }

        .notification-item.unread {
            background: rgba(30, 41, 59, 0.8);
            border-left: 3px solid var(--accent-secondary);
        }

        .notification-time {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .btn-custom {
            background: var(--accent-color);
            color: #0f172a;
            border: none;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-custom:hover {
            background: #4fd1c5;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(100, 255, 218, 0.3);
        }

        .btn-outline-custom {
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
            background: transparent;
        }

        .btn-outline-custom:hover {
            background: var(--accent-color);
            color: #0f172a;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 0;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--accent-color);
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row h-100">
            <!-- Include Sidebar -->
            <?php include 'sidebar.php'; ?>
            
            <!-- Main Content -->
            <div class="main-content animate__animated animate__fadeIn">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-0" style="font-weight: 700; letter-spacing: 1px; color: var(--accent-color); font-size: 1.5rem; text-transform: uppercase;">Notifications</h4>
                        <p class="text-white mb-0">Manage your notifications</p>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="mark_all_read">
                            <button type="submit" class="btn btn-outline-custom btn-sm">
                                <i class="bi bi-check2-all me-1"></i> Mark All as Read
                            </button>
                        </form>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear all notifications? This action cannot be undone.');">
                            <input type="hidden" name="action" value="clear_all">
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash me-1"></i> Clear All
                            </button>
                        </form>
                    </div>
                </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card animate__animated animate__fadeInUp">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Your Notifications</h5>
                        <span class="badge bg-danger"><?php echo $unread_count; ?> unread</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (count($notifications) > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($notifications as $notification): ?>
                                    <div class="list-group-item border-0 p-0">
                                        <div class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="me-3">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                                                    <small class="notification-time">
                                                        <i class="bi bi-clock me-1"></i> 
                                                        <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                                                    </small>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <?php if (!$notification['is_read']): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="action" value="mark_as_read">
                                                            <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as read">
                                                                <i class="bi bi-check2"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-bell-slash"></i>
                                <h5>No notifications yet</h5>
                                <p class="mb-0">You don't have any notifications at the moment.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update notification count in the sidebar
        function updateNotificationCount() {
            const unreadCount = <?php echo $unread_count; ?>;
            const badge = document.querySelector('.sidebar .nav-item[href="notifications.php"] .badge');
            if (badge) {
                badge.textContent = unreadCount;
                if (unreadCount > 0) {
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            }
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Update notification count when page loads
            updateNotificationCount();
        });
    </script>
</body>
</html>

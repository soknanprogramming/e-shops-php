<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/UserRepository.php';

// 1. Auth Check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// 2. Handle Search and Ordering parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : null;
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'id_desc';

// 3. Fetch Users using Repository
$userRepo = new UserRepository($conn);
$users = $userRepo->getAllWithFilters($filter, $search, $orderBy);

// Pending count for sidebar badge
$stmtPending = $conn->prepare("SELECT COUNT(*) as total FROM User WHERE request_post_permission = 1 AND (can_post = 0 OR can_post IS NULL)");
$stmtPending->execute();
$pendingCount = $stmtPending->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="icon" href="../icon/e-commerce-logo.png" sizes="any" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3325;
            --primary-container: #2a5038;
            --primary-light: rgba(26, 51, 37, 0.05);
            --secondary: #9d7c39;
            --secondary-light: rgba(157, 124, 57, 0.1);
            --tertiary: #7e000a;
            --bg-body: #faf7f2;
            --surface: #ffffff;
            --on-surface: #201b09;
            --on-surface-variant: #6b6355;
            --outline: rgba(74, 69, 56, 0.12);
            --outline-strong: rgba(74, 69, 56, 0.25);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --font-headline: 'Manrope', sans-serif;
            --font-body: 'Public Sans', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            margin: 0;
            padding: 0;
            background-color: var(--bg-body);
            color: var(--on-surface);
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex-grow: 1;
            padding: 1.5rem 3rem;
            width: 100%;
        }

        @media (max-width: 992px) { .main-content { padding: 1.25rem 2rem; } }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .main-content { padding: 1rem; }
        }

        @media (max-width: 480px) {
            .main-content { padding: 0.75rem; }
        }

        /* Page Header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .page-header h1 {
            font-family: var(--font-headline);
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
        }

        .page-header .count-badge {
            font-size: 0.8rem;
            color: var(--on-surface-variant);
            font-weight: 600;
            background: var(--surface);
            border: 1px solid var(--outline);
            padding: 6px 14px;
            border-radius: 20px;
        }

        @media (max-width: 768px) {
            .page-header h1 { font-size: 1.25rem; }
            .page-header .count-badge { font-size: 0.75rem; padding: 5px 12px; }
        }

        @media (max-width: 480px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            .page-header .count-badge {
                align-self: flex-start;
            }
        }

        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            gap: 0;
            margin-bottom: 1.25rem;
            background: var(--surface);
            border: 1px solid var(--outline);
            border-radius: var(--radius-sm);
            overflow: hidden;
            width: fit-content;
            max-width: 100%;
        }

        .filter-tab {
            padding: 8px 18px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--on-surface-variant);
            transition: all 0.2s;
            border-right: 1px solid var(--outline);
            white-space: nowrap;
        }

        .filter-tab:last-child { border-right: none; }

        .filter-tab:hover { background: var(--primary-light); color: var(--primary); }

        .filter-tab.active {
            background: var(--primary);
            color: #fff;
        }

        @media (max-width: 768px) {
            .filter-tabs {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .filter-tab {
                padding: 8px 14px;
                font-size: 0.75rem;
            }
        }

        /* Search and Order Controls */
        .search-order-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 16px 10px 42px;
            border: 1px solid var(--outline);
            border-radius: var(--radius-sm);
            background: var(--surface);
            font-family: var(--font-body);
            font-size: 0.85rem;
            color: var(--on-surface);
            transition: all 0.2s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .search-box input::placeholder {
            color: var(--on-surface-variant);
        }

        .search-box svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--on-surface-variant);
            pointer-events: none;
        }

        .order-select {
            padding: 10px 16px;
            border: 1px solid var(--outline);
            border-radius: var(--radius-sm);
            background: var(--surface);
            font-family: var(--font-body);
            font-size: 0.85rem;
            color: var(--on-surface);
            cursor: pointer;
            transition: all 0.2s;
            min-width: 180px;
        }

        .order-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        @media (max-width: 768px) {
            .search-box {
                min-width: 200px;
            }
            .order-select {
                min-width: 150px;
                font-size: 0.8rem;
                padding: 8px 12px;
            }
        }

        @media (max-width: 480px) {
            .search-order-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .search-box {
                min-width: 100%;
            }
            .order-select {
                min-width: 100%;
            }
        }

        /* Table Card */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--outline);
            border-radius: var(--radius-md);
            overflow: hidden;
        }

        .table-responsive { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 12px 16px;
            text-align: left;
            background: var(--bg-body);
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--on-surface-variant);
            border-bottom: 1px solid var(--outline);
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--outline);
            font-size: 0.85rem;
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover { background: var(--primary-light); }

        .user-name {
            font-weight: 600;
            color: var(--on-surface);
        }

        .user-email {
            font-size: 0.8rem;
            color: var(--on-surface-variant);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            display: inline-block;
        }

        .badge-admin { background: rgba(157, 124, 57, 0.12); color: var(--secondary); }
        .badge-user { background: rgba(108, 117, 125, 0.12); color: #6c757d; }
        .badge-allowed { background: rgba(40, 167, 69, 0.12); color: #28a745; }
        .badge-restricted { background: rgba(108, 117, 125, 0.12); color: #6c757d; }
        .badge-requesting { background: rgba(255, 193, 7, 0.15); color: #856404; margin-left: 6px; }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-allow { background: rgba(40, 167, 69, 0.12); color: #28a745; }
        .btn-allow:hover { background: rgba(40, 167, 69, 0.2); }
        .btn-revoke { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
        .btn-revoke:hover { background: rgba(220, 53, 69, 0.2); }
        .btn-role { background: rgba(26, 51, 37, 0.08); color: var(--primary); }
        .btn-role:hover { background: rgba(26, 51, 37, 0.15); }

        @media (max-width: 768px) {
            .action-btn {
                padding: 5px 10px;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .action-btn {
                padding: 4px 8px;
                font-size: 0.65rem;
                gap: 3px;
            }
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--on-surface-variant);
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            opacity: 0.3;
            margin-bottom: 0.75rem;
        }

        .empty-state p { margin: 0; font-size: 0.875rem; }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 10px 18px;
            border-radius: var(--radius-sm);
            color: #fff;
            font-weight: 600;
            font-size: 0.825rem;
            z-index: 9999;
            animation: toastIn 0.3s ease, toastOut 0.3s ease 2.7s forwards;
        }

        .toast-success { background: var(--primary); }
        .toast-error { background: var(--tertiary); }

        @keyframes toastIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes toastOut { from { opacity: 1; } to { opacity: 0; transform: translateY(10px); } }
    </style>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>

    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1>User Management</h1>
            <span class="count-badge"><?php echo count($users); ?> user<?php echo count($users) !== 1 ? 's' : ''; ?></span>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="admin_user.php" class="filter-tab <?php echo !isset($_GET['filter']) ? 'active' : ''; ?>">All Users</a>
            <a href="admin_user.php?filter=requesting" class="filter-tab <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'requesting') ? 'active' : ''; ?>">
                Pending Requests
                <?php if ($pendingCount > 0): ?>
                    <span style="margin-left: 4px; opacity: 0.7;">(<?php echo $pendingCount; ?>)</span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Search and Order Controls -->
        <form method="GET" class="search-order-bar" id="userFilterForm">
            <?php if (isset($_GET['filter'])): ?>
                <input type="hidden" name="filter" value="<?php echo htmlspecialchars($_GET['filter']); ?>">
            <?php endif; ?>
            
            <div class="search-box">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" name="search" placeholder="Search users by name or email..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <select name="order" class="order-select" id="orderSelect">
                <option value="id_desc" <?php echo $orderBy === 'id_desc' ? 'selected' : ''; ?>>Newest First</option>
                <option value="id_asc" <?php echo $orderBy === 'id_asc' ? 'selected' : ''; ?>>Oldest First</option>
                <option value="name_asc" <?php echo $orderBy === 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                <option value="name_desc" <?php echo $orderBy === 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                <option value="role_asc" <?php echo $orderBy === 'role_asc' ? 'selected' : ''; ?>>Role (User → Admin)</option>
                <option value="role_desc" <?php echo $orderBy === 'role_desc' ? 'selected' : ''; ?>>Role (Admin → User)</option>
                <option value="permission_asc" <?php echo $orderBy === 'permission_asc' ? 'selected' : ''; ?>>Permission (Restricted → Allowed)</option>
                <option value="permission_desc" <?php echo $orderBy === 'permission_desc' ? 'selected' : ''; ?>>Permission (Allowed → Restricted)</option>
            </select>
        </form>

        <!-- Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="?order=<?php echo ($orderBy === 'id_asc') ? 'id_desc' : 'id_asc'; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo isset($_GET['filter']) ? '&filter=' . htmlspecialchars($_GET['filter']) : ''; ?>" 
                                   style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 4px;">
                                    ID
                                    <?php if (strpos($orderBy, 'id') !== false): ?>
                                        <span><?php echo $orderBy === 'id_asc' ? '↑' : '↓'; ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?order=<?php echo ($orderBy === 'name_asc') ? 'name_desc' : 'name_asc'; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo isset($_GET['filter']) ? '&filter=' . htmlspecialchars($_GET['filter']) : ''; ?>" 
                                   style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 4px;">
                                    Name
                                    <?php if (strpos($orderBy, 'name') !== false): ?>
                                        <span><?php echo $orderBy === 'name_asc' ? '↑' : '↓'; ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?order=<?php echo ($orderBy === 'role_asc') ? 'role_desc' : 'role_asc'; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo isset($_GET['filter']) ? '&filter=' . htmlspecialchars($_GET['filter']) : ''; ?>" 
                                   style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 4px;">
                                    Role
                                    <?php if (strpos($orderBy, 'role') !== false): ?>
                                        <span><?php echo $orderBy === 'role_asc' ? '↑' : '↓'; ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?order=<?php echo ($orderBy === 'permission_asc') ? 'permission_desc' : 'permission_asc'; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo isset($_GET['filter']) ? '&filter=' . htmlspecialchars($_GET['filter']) : ''; ?>" 
                                   style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 4px;">
                                    Posting Permission
                                    <?php if (strpos($orderBy, 'permission') !== false): ?>
                                        <span><?php echo $orderBy === 'permission_asc' ? '↑' : '↓'; ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        <p>No users found.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td>
                                    <div>
                                        <div class="user-name"><?php echo htmlspecialchars($user['name']); ?></div>
                                        <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($user['is_admin']): ?>
                                        <span class="badge badge-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-user">User</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['can_post']): ?>
                                        <span class="badge badge-allowed">Allowed</span>
                                    <?php else: ?>
                                        <span class="badge badge-restricted">Restricted</span>
                                        <?php if (isset($user['request_post_permission']) && $user['request_post_permission'] == 1): ?>
                                            <span class="badge badge-requesting">Requesting</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px;">
                                        <a href="../controllers/user.php?action=toggle_permission&id=<?php echo $user['id']; ?>" class="action-btn <?php echo $user['can_post'] ? 'btn-revoke' : 'btn-allow'; ?>">
                                            <?php echo $user['can_post'] ? 'Revoke' : 'Allow'; ?>
                                        </a>
                                        <a href="../controllers/user.php?action=toggle_role&id=<?php echo $user['id']; ?>" class="action-btn btn-role">
                                            Toggle Role
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <?php if (isset($_GET['success']) || isset($_GET['error'])): ?>
        <div class="toast <?php echo isset($_GET['success']) ? 'toast-success' : 'toast-error'; ?>">
            <?php echo htmlspecialchars($_GET['success'] ?? $_GET['error'] ?? ''); ?>
        </div>
    <?php endif; ?>

    <script>
        // Auto-submit form when order selection changes
        document.getElementById('orderSelect').addEventListener('change', function() {
            document.getElementById('userFilterForm').submit();
        });

        // Add debounce utility for search input
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Auto-submit search after 500ms delay
        const searchInput = document.querySelector('.search-box input');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function() {
                document.getElementById('userFilterForm').submit();
            }, 500));
        }
    </script>
</body>
</html>

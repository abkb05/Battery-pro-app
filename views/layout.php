<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark' ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'BatteryPro Management System') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <link href="<?= site_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <?php if (check_login()): ?>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="<?= site_url('/') ?>" class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <span class="brand-text">BatteryPro</span>
                </a>
            </div>

            <div class="sidebar-divider"></div>

            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/') ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/suppliers') ?>">
                        <i class="fas fa-truck"></i>
                        <span>Suppliers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/batteries') ?>">
                        <i class="fas fa-car-battery"></i>
                        <span>Inventory</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/sales') ?>">
                        <i class="fas fa-cash-register"></i>
                        <span>Sales</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/customers') ?>">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/expenses') ?>">
                        <i class="fas fa-coins"></i>
                        <span>Expenses</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/purchase-orders') ?>">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Purchase Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/returns') ?>">
                        <i class="fas fa-undo"></i>
                        <span>Returns</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/exchange') ?>">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Exchange/Scrap</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/reports') ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/warranties') ?>">
                        <i class="fas fa-shield-alt"></i>
                        <span>Warranties</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/backups') ?>">
                        <i class="fas fa-database"></i>
                        <span>Backups</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/audit') ?>">
                        <i class="fas fa-history"></i>
                        <span>Audit Log</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/settings') ?>">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="sidebar-divider"></div>
                <div class="d-flex align-items-center px-3 py-2 gap-2">
                    <button class="btn btn-sm btn-outline-light flex-fill" onclick="switchTheme('light')" title="Light"><i class="fas fa-sun"></i></button>
                    <button class="btn btn-sm btn-outline-light flex-fill" onclick="switchTheme('dark')" title="Dark"><i class="fas fa-moon"></i></button>
                </div>
                <div class="user-info px-3 py-2">
                    <i class="fas fa-user-circle fa-lg me-2"></i>
                    <div class="user-text">
                        <small class="fw-medium d-block text-truncate"><?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?></small>
                        <small class="opacity-75"><?= ucfirst($_SESSION['user_role'] ?? 'staff') ?></small>
                    </div>
                    <a href="<?= site_url('/logout') ?>" class="ms-auto text-white-50 logout-icon" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-wrapper" id="mainWrapper">
            <!-- Top bar -->
            <nav class="topbar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-link text-dark sidebar-toggle me-2" id="sidebarToggle">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <span class="fw-semibold"><?= htmlspecialchars($title ?? 'Dashboard') ?></span>
                </div>
                <div>
                    <a href="<?= site_url('/profile') ?>" class="btn btn-sm btn-outline-secondary me-2" title="Profile">
                        <i class="fas fa-id-card"></i>
                    </a>
                </div>
            </nav>

            <!-- Flash Messages -->
            <?php
            $flash = get_flash_message();
            if (!empty($flash)):
                foreach ($flash as $type => $messages):
                    foreach ($messages as $msg):
            ?>
            <div class="alert alert-<?= htmlspecialchars($type) ?> alert-dismissible fade show rounded-0 border-0 mb-0" role="alert">
                <div class="container-fluid">
                    <i class="fas fa-<?= $type === 'success' ? 'check-circle' : ($type === 'error' || $type === 'danger' ? 'exclamation-circle' : 'info-circle') ?> me-2"></i>
                    <?= htmlspecialchars($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php
                    endforeach;
                endforeach;
            endif;
            ?>

            <div class="main-content">
                <?= $content ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Not logged in - just show content (login page) -->
    <?php
    $flash = get_flash_message();
    ?>
    <?= $content ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="<?= site_url('assets/js/app.js') ?>"></script>
    <script>
        function switchTheme(theme) {
            $.post('<?= site_url('api/theme.php') ?>', {theme: theme}, function() {
                location.reload();
            });
        }
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainWrapper').classList.toggle('expanded');
        });
    </script>
</body>
</html>

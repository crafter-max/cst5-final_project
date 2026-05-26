<?php
// header.php — Shared page header.
// Required at the top of every view file.
// Starts the session once (safe to call from any view), then outputs
// the HTML <head> and the navbar (only when the user is logged in).

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paragryph InvSys</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Paragryph InvSys theme -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if (isset($_SESSION['account_id'])): ?>
<!-- ── Navbar (authenticated) ───────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="dashboard.php">
            Paragryph&nbsp;<span class="accent">InvSys</span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <!-- Left links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"
                       href="dashboard.php">
                        <i class="bi bi-boxes me-1"></i>Products
                    </a>
                </li>
            </ul>

            <!-- Right: workspace badge + user info + logout -->
            <div class="d-flex align-items-center gap-3">
                <span class="nav-ws-badge">
                    <i class="bi bi-building me-1"></i>
                    WS:&nbsp;<?= htmlspecialchars($_SESSION['workspace_id']) ?>
                </span>
                <div class="text-end d-none d-lg-block">
                    <div class="text-white small fw-semibold lh-1">
                        <?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?>
                    </div>
                    <div class="mt-1" style="font-size:0.72rem; color:#576478;">
                        <?= htmlspecialchars($_SESSION['email']) ?>
                    </div>
                </div>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="page-content">
<div class="container-fluid px-4">

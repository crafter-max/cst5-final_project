<?php
// Shared page header — included by every view file.
// Starts the session and renders the HTML head + authenticated navbar.

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<?php if (isset($_SESSION['account_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">

        <a class="navbar-brand" href="/view/dashboard.php">
            Paragryph&nbsp;<span class="accent">InvSys</span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"
                       href="/view/dashboard.php">
                        <i class="bi bi-boxes me-1"></i>Products
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="nav-ws-badge">
                    <i class="bi bi-building me-1"></i>
                    WS:&nbsp;<?= htmlspecialchars($_SESSION['workspace_id']) ?>
                </span>
                <div class="text-end d-none d-lg-block">
                    <div class="text-white small fw-semibold lh-1">
                        <?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?>
                    </div>
                    <div class="mt-1" style="font-size:0.72rem; color:var(--text-muted);">
                        <?= htmlspecialchars($_SESSION['email']) ?>
                    </div>
                </div>
                <a href="/controller/acc/logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>

    </div>
</nav>
<?php endif; ?>

<div class="page-content">
<div class="container-fluid px-4">

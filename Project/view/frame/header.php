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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --brand-dark:   #12172a;
            --brand-mid:    #1a2238;
            --brand-accent: #4a8ef5;
        }

        /* ── Layout ──────────────────────────────────────────── */
        body {
            background-color: #f0f2f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .page-content {
            flex: 1;
            padding: 2rem 0;
        }

        /* ── Navbar ──────────────────────────────────────────── */
        .navbar-brand .accent { color: var(--brand-accent); }
        .nav-ws-badge {
            font-size: .7rem;
            background: rgba(255,255,255,.12);
            border-radius: 4px;
            padding: 2px 7px;
            color: #aab4c8;
        }

        /* ── Cards ───────────────────────────────────────────── */
        .card {
            border: none;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            border-radius: 10px;
        }
        .card-header-custom {
            background: linear-gradient(135deg, var(--brand-mid), #233054);
            color: #fff;
            border-radius: 10px 10px 0 0 !important;
            padding: .85rem 1.25rem;
        }

        /* ── Buttons ─────────────────────────────────────────── */
        .btn-brand {
            background-color: var(--brand-accent);
            border-color:     var(--brand-accent);
            color: #fff;
        }
        .btn-brand:hover {
            background-color: #3279e0;
            border-color:     #3279e0;
            color: #fff;
        }

        /* ── Table ───────────────────────────────────────────── */
        .table thead th {
            background-color: #e8ecf5;
            font-weight: 600;
            font-size: .85rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #4a5568;
        }
        .table-hover tbody tr:hover { background-color: #eef3ff; }

        /* ── Status badges ───────────────────────────────────── */
        .badge-active    { background-color: #198754; color: #fff; }
        .badge-inactive  { background-color: #6c757d; color: #fff; }
        .badge-low_stock { background-color: #e9a820; color: #fff; }

        /* ── Alerts ──────────────────────────────────────────── */
        .alert { border-radius: 8px; }

        /* ── Auth pages ──────────────────────────────────────── */
        .auth-wrapper {
            min-height: calc(100vh - 130px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card { width: 100%; max-width: 460px; }
        .auth-logo {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--brand-dark);
        }
        .auth-logo span { color: var(--brand-accent); }
    </style>
</head>
<body>

<?php if (isset($_SESSION['account_id'])): ?>
<!-- ── Navbar (authenticated) ───────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm"
     style="background-color: var(--brand-mid);">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            Paragryph&nbsp;<span class="accent">InvSys</span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <!-- Left links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-boxes me-1"></i>Products
                    </a>
                </li>
            </ul>

            <!-- Right: user info + logout -->
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="text-white small fw-semibold lh-1">
                        <?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?>
                    </div>
                    <div class="mt-1">
                        <span class="nav-ws-badge">
                            <i class="bi bi-building me-1"></i>WS:&nbsp;<?= htmlspecialchars($_SESSION['workspace_id']) ?>
                        </span>
                    </div>
                </div>
                <a href="logout.php" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="page-content">
<div class="container-fluid px-4">

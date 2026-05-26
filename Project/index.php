<?php
// index.php — Login page view.
// Starts the session and immediately redirects to dashboard if already logged in.
// Otherwise renders the login form, which POSTs to login.php.

require_once 'header.php'; // also starts session

if (isset($_SESSION['account_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Pick up any status message forwarded from login.php via session flash.
$error = '';
if (!empty($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
?>

<div class="auth-wrapper">
    <div class="auth-card card p-4 p-md-5 shadow">

        <!-- Brand -->
        <div class="text-center mb-4">
            <div class="auth-logo">
                Paragryph&nbsp;<span>InvSys</span>
            </div>
            <p class="text-muted small mt-1">Inventory Management System</p>
        </div>

        <!-- Error alert -->
        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Login form -->
        <form action="login.php" method="POST" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="you@company.com" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
            </button>
        </form>

        <hr class="my-4">

        <p class="text-center small text-muted mb-0">
            No account yet?
            <a href="register.php" class="fw-semibold text-decoration-none">Register here</a>
        </p>

    </div>
</div>

<?php require_once 'footer.php'; ?>

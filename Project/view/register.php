<?php
// register.php — Account registration view.
// Renders the registration form. POSTs to accReg.php.
// Already-logged-in users are bounced to dashboard.

require_once 'header.php'; // also starts session

if (isset($_SESSION['account_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Flash messages forwarded from accReg.php.
$error   = '';
$success = '';
if (!empty($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
if (!empty($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

// Sticky field values: repopulate the form after a failed submission.
$old = $_SESSION['flash_old'] ?? [];
unset($_SESSION['flash_old']);
?>

<div class="auth-wrapper" style="align-items: flex-start; padding: 2rem 0;">
    <div class="auth-card card p-4 p-md-5 shadow" style="max-width: 560px;">

        <!-- Brand -->
        <div class="text-center mb-4">
            <div class="auth-logo">
                Paragryph&nbsp;<span>InvSys</span>
            </div>
            <p class="text-muted small mt-1">Create a new account</p>
        </div>

        <!-- Alerts -->
        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success py-2 small">
                <i class="bi bi-check-circle me-1"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Registration form -->
        <form action="accReg.php" method="POST" novalidate>

            <!-- Workspace ID -->
            <div class="mb-3">
                <label for="workspace_id" class="form-label fw-semibold">
                    Workspace ID <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <input type="text" class="form-control" id="workspace_id" name="workspace_id"
                           placeholder="Your workspace identifier"
                           value="<?= htmlspecialchars($old['workspace_id'] ?? '') ?>"
                           required>
                </div>
                <div class="form-text text-muted">
                    This must match a pre-existing workspace in the system.
                </div>
            </div>

            <!-- Name row -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label for="first_name" class="form-label fw-semibold">
                        First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                           placeholder="Juan"
                           value="<?= htmlspecialchars($old['first_name'] ?? '') ?>"
                           required>
                </div>
                <div class="col-6">
                    <label for="last_name" class="form-label fw-semibold">
                        Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                           placeholder="Dela Cruz"
                           value="<?= htmlspecialchars($old['last_name'] ?? '') ?>"
                           required>
                </div>
            </div>

            <!-- Contact -->
            <div class="mb-3">
                <label for="contact" class="form-label fw-semibold">
                    Contact Number <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                    <input type="text" class="form-control" id="contact" name="contact"
                           placeholder="+63 9XX XXX XXXX"
                           value="<?= htmlspecialchars($old['contact'] ?? '') ?>"
                           required>
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                    Email Address <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="you@company.com"
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                           required>
                </div>
            </div>

            <!-- Hire date -->
            <div class="mb-3">
                <label for="hire_date" class="form-label fw-semibold">
                    Hire Date <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                    <input type="date" class="form-control" id="hire_date" name="hire_date"
                           value="<?= htmlspecialchars($old['hire_date'] ?? '') ?>"
                           required>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">
                    Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Min. 8 characters" required>
                </div>
            </div>

            <!-- Confirm password -->
            <div class="mb-4">
                <label for="confirm_password" class="form-label fw-semibold">
                    Confirm Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" id="confirm_password"
                           name="confirm_password" placeholder="Repeat password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2 fw-semibold">
                <i class="bi bi-person-plus me-1"></i>Create Account
            </button>
        </form>

        <hr class="my-4">

        <p class="text-center small text-muted mb-0">
            Already have an account?
            <a href="index.php" class="fw-semibold text-decoration-none">Sign in</a>
        </p>

    </div>
</div>

<?php require_once 'footer.php'; ?>

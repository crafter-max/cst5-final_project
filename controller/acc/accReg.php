<?php

require_once __DIR__ . '/../../model/account.php'; // also loads db.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../view/register.php');
    exit;
}

// ── Collect & sanitize input ──────────────────────────────────
$fields = ['workspace_id', 'first_name', 'last_name', 'contact', 'email', 'hire_date'];
$data   = [];
foreach ($fields as $f) {
    $data[$f] = trim($_POST[$f] ?? '');
}
$password         = $_POST['password']         ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// ── Validation ────────────────────────────────────────────────
$errors = [];

foreach ($fields as $f) {
    if (empty($data[$f])) {
        $errors[] = ucfirst(str_replace('_', ' ', $f)) . ' is required.';
    }
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}

if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
}

if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match.';
}

if (!empty($data['hire_date']) && !strtotime($data['hire_date'])) {
    $errors[] = 'Hire date is not a valid date.';
}

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    $_SESSION['flash_old']   = $data; // repopulate form fields
    header('Location: ../../view/register.php');
    exit;
}

// ── Business-logic checks ─────────────────────────────────────
$account = new Account();

if (!$account->workspaceExists($data['workspace_id'])) {
    $_SESSION['flash_error'] = 'Workspace ID does not exist. Contact your administrator.';
    $_SESSION['flash_old']   = $data;
    header('Location: ../../view/register.php');
    exit;
}

if ($account->emailExists($data['email'])) {
    $_SESSION['flash_error'] = 'That email address is already registered.';
    $_SESSION['flash_old']   = $data;
    header('Location: ../../view/register.php');
    exit;
}

// ── Persist ───────────────────────────────────────────────────
$account->workspace_id = $data['workspace_id'];
$account->first_name   = $data['first_name'];
$account->last_name    = $data['last_name'];
$account->contact      = $data['contact'];
$account->email        = $data['email'];
$account->hire_date    = $data['hire_date'];
$account->password     = $password; // Account::create() hashes this

if ($account->create()) {
    $_SESSION['flash_success'] = 'Account created successfully. You can now sign in.';
    header('Location: ../../view/register.php');
} else {
    $_SESSION['flash_error'] = 'Registration failed. Please try again.';
    $_SESSION['flash_old']   = $data;
    header('Location: ../../view/register.php');
}
exit;

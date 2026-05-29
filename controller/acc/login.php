<?php

require_once __DIR__ . '/../../model/account.php'; // also loads db.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php');
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

// ── Basic presence check ──────────────────────────────────────
if (empty($email) || empty($password)) {
    $_SESSION['flash_error'] = 'Email and password are required.';
    header('Location: ../../index.php');
    exit;
}

// ── Look up account ───────────────────────────────────────────
$account = new Account();
$row     = $account->findByEmail($email);

if (!$row || !password_verify($password, $row['password'])) {
    $_SESSION['flash_error'] = 'Invalid email or password.';
    header('Location: ../../index.php');
    exit;
}

// ── Populate session ──────────────────────────────────────────
session_regenerate_id(true); // prevent session fixation

$_SESSION['account_id']   = $row['account_id'];
$_SESSION['workspace_id'] = $row['workspace_id'];
$_SESSION['first_name']   = $row['first_name'];
$_SESSION['last_name']    = $row['last_name'];
$_SESSION['email']        = $row['email'];

header('Location: ../../view/dashboard.php');
exit;

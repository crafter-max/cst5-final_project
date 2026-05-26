<?php
// del_prd.php — Delete product controller.
// Accepts a GET request with ?id= (confirmed via JS dialog in dashboard/prd_view).
// Deletes the product only if it belongs to the session workspace_id.
// Always redirects to dashboard.php with a flash message.

require_once '../../model/product.php'; // also loads db.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Auth guard ────────────────────────────────────────────────
if (!isset($_SESSION['account_id'])) {
    header('Location: ../../index.php');
    exit;
}

// ── Validate id ───────────────────────────────────────────────
$product_id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$workspace_id = $_SESSION['workspace_id'];

if ($product_id <= 0) {
    $_SESSION['flash_error'] = 'Invalid product.';
    header('Location: ../../view/dashboard.php');
    exit;
}

// ── Execute delete (scoped to workspace) ──────────────────────
$product = new Product();
$deleted = $product->delete($product_id, $workspace_id);

if ($deleted) {
    $_SESSION['flash_success'] = 'Product deleted successfully.';
} else {
    // Either it didn't exist or belongs to another workspace — same response.
    $_SESSION['flash_error'] = 'Product could not be deleted or was not found.';
}

header('Location: ../../view/dashboard.php');
exit;

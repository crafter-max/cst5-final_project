<?php
// cre_upd_prd.php — Create or update product controller.
// Receives POST from add_upd.php.
// Decides INSERT vs UPDATE by querying the DB for product_id existence
// under the session workspace_id — NOT by trusting the form alone.
// workspace_id always comes from $_SESSION, never from form input.

require_once '../../model/product.php'; // also loads db.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Auth guard ────────────────────────────────────────────────
if (!isset($_SESSION['account_id'])) {
    header('Location: ../../index.php');
    exit;
}

// ── Accept POST only ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../view/dashboard.php');
    exit;
}

$workspace_id = $_SESSION['workspace_id']; // never from form

// ── Collect & sanitize fields ─────────────────────────────────
$product_id  = isset($_POST['product_id']) && $_POST['product_id'] !== ''
               ? (int)$_POST['product_id']
               : null;

$sku          = trim($_POST['sku']          ?? '');
$product_name = trim($_POST['product_name'] ?? '');
$category     = trim($_POST['category']     ?? '');
$description  = trim($_POST['description']  ?? '');
$supplier     = trim($_POST['supplier']     ?? '');
$quantity     = trim($_POST['quantity']     ?? '');
$unit_price   = trim($_POST['unit_price']   ?? '');
$status       = trim($_POST['status']       ?? 'active');

// ── Validation ────────────────────────────────────────────────
$errors = [];

if (empty($sku))          $errors[] = 'SKU is required.';
if (empty($product_name)) $errors[] = 'Product name is required.';
if (empty($category))     $errors[] = 'Category is required.';

if ($quantity === '' || !is_numeric($quantity) || (int)$quantity < 0) {
    $errors[] = 'Quantity must be a non-negative number.';
}
if ($unit_price === '' || !is_numeric($unit_price) || (float)$unit_price < 0) {
    $errors[] = 'Unit price must be a non-negative number.';
}

$allowed_statuses = ['active', 'inactive', 'low_stock'];
if (!in_array($status, $allowed_statuses, true)) {
    $status = 'active';
}

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    // Sticky values so the form re-renders with user's input.
    $_SESSION['flash_old'] = [
        'sku' => $sku, 'product_name' => $product_name, 'category' => $category,
        'description' => $description, 'supplier' => $supplier,
        'quantity' => $quantity, 'unit_price' => $unit_price, 'status' => $status,
    ];
    $redirect = ($product_id !== null)
        ? "../../view/add_upd.php?id={$product_id}"
        : "../../view/add_upd.php";
    header("Location: {$redirect}");
    exit;
}

// ── Determine operation via DB check ─────────────────────────
// This is the authoritative check — never trust a hidden form field alone.
$pdo_product  = new Product();
$is_update    = ($product_id !== null) && $pdo_product->exists($product_id, $workspace_id);

// ── Populate model ────────────────────────────────────────────
$pdo_product->workspace_id = $workspace_id;
$pdo_product->sku          = $sku;
$pdo_product->product_name = $product_name;
$pdo_product->category     = $category;
$pdo_product->description  = $description;
$pdo_product->supplier     = $supplier;
$pdo_product->quantity     = (int)$quantity;
$pdo_product->unit_price   = (float)$unit_price;
$pdo_product->status       = $status;

// ── Execute ───────────────────────────────────────────────────
if ($is_update) {
    $pdo_product->product_id = $product_id;
    $success = $pdo_product->update();
    $msg_ok  = 'Product updated successfully.';
    $msg_err = 'Failed to update product. Please try again.';
} else {
    $success = $pdo_product->create();
    $msg_ok  = 'Product added successfully.';
    $msg_err = 'Failed to add product. Please try again.';
}

// ── Redirect ──────────────────────────────────────────────────
if ($success) {
    $_SESSION['flash_success'] = $msg_ok;
} else {
    $_SESSION['flash_error'] = $msg_err;
}

header('Location: ../../view/dashboard.php');
exit;

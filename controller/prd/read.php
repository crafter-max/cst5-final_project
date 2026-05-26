<?php
// read.php — Product list controller.
// Not accessed directly — always require'd by dashboard.php.
// Reads filter inputs from GET, queries Product::getAll() scoped
// to the session workspace_id, and sets:
//   $products  — array of product rows
//   $categories — distinct category list for the dropdown
//   $filters   — active filter values for re-rendering the form

// Guard: must be require'd, not accessed directly.
if (!isset($_SESSION['account_id'])) {
    header('Location: ../../index.php');
    exit;
}

require_once __DIR__ . '/../../model/product.php';

$product = new Product();

// ── Active filters (from GET) ─────────────────────────────────
$filters = [
    'search'   => trim($_GET['search']   ?? ''),
    'category' => trim($_GET['category'] ?? ''),
    'status'   => trim($_GET['status']   ?? ''),
];

// ── Query ─────────────────────────────────────────────────────
$workspace_id = $_SESSION['workspace_id'];
$products     = $product->getAll($workspace_id, $filters);
$categories   = $product->getCategories($workspace_id);

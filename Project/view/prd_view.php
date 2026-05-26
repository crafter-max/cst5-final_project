<?php
// prd_view.php — Single product detail view.
// Reads ?id= from GET, fetches the product (scoped to session workspace_id),
// and displays it like an online-shop product page.
// Provides an Edit button linking to add_upd.php and a Back link to dashboard.

require_once 'header.php'; // starts session

if (!isset($_SESSION['account_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'product.php';

$product_id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$workspace_id = $_SESSION['workspace_id'];

if ($product_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

$product = new Product();
$p       = $product->getById($product_id, $workspace_id);

// Product not found or belongs to another workspace — bounce back.
if (!$p) {
    $_SESSION['flash_error'] = 'Product not found or access denied.';
    header('Location: dashboard.php');
    exit;
}

// Status badge helper.
$status_label = ucfirst(str_replace('_', ' ', $p['status']));
?>

<!-- ── Breadcrumb ────────────────────────────────────────────── -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="dashboard.php">Products</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($p['product_name']) ?></li>
    </ol>
</nav>

<!-- ── Product Card ─────────────────────────────────────────── -->
<div class="card">
    <!-- Header bar -->
    <div class="card-header-custom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <span class="fw-bold fs-5"><?= htmlspecialchars($p['product_name']) ?></span>
            <span class="ms-2 text-white-50 small">SKU: <?= htmlspecialchars($p['sku']) ?></span>
        </div>
        <span class="badge badge-<?= htmlspecialchars($p['status']) ?> fs-6">
            <?= $status_label ?>
        </span>
    </div>

    <div class="card-body p-4">
        <div class="row g-4">

            <!-- Left column: core details -->
            <div class="col-12 col-lg-7">

                <!-- Description -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted small fw-semibold mb-2 border-bottom pb-1">
                        Description
                    </h6>
                    <p class="mb-0" style="white-space: pre-wrap;">
                        <?= !empty($p['description'])
                            ? htmlspecialchars($p['description'])
                            : '<em class="text-muted">No description provided.</em>' ?>
                    </p>
                </div>

                <!-- Category & Supplier -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="p-3 rounded" style="background:#f0f2f6;">
                            <div class="text-muted small">Category</div>
                            <div class="fw-semibold"><?= htmlspecialchars($p['category']) ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded" style="background:#f0f2f6;">
                            <div class="text-muted small">Supplier</div>
                            <div class="fw-semibold"><?= htmlspecialchars($p['supplier'] ?: '—') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Dates -->
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded" style="background:#f0f2f6;">
                            <div class="text-muted small">Date Added</div>
                            <div class="fw-semibold">
                                <?= date('M d, Y', strtotime($p['date_added'])) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded" style="background:#f0f2f6;">
                            <div class="text-muted small">Last Updated</div>
                            <div class="fw-semibold">
                                <?= date('M d, Y h:i A', strtotime($p['last_updated'])) ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right column: pricing & stock panel -->
            <div class="col-12 col-lg-5">
                <div class="rounded-3 p-4 h-100"
                     style="background: linear-gradient(135deg, #1a2238, #233054); color: #fff;">

                    <div class="mb-4">
                        <div class="text-white-50 small mb-1">Unit Price</div>
                        <div class="display-6 fw-bold text-white">
                            ₱<?= number_format((float)$p['unit_price'], 2) ?>
                        </div>
                    </div>

                    <hr style="border-color: rgba(255,255,255,.15);">

                    <div class="mb-3">
                        <div class="text-white-50 small mb-1">Stock Quantity</div>
                        <?php
                            $qty = (int)$p['quantity'];
                            $qty_color = $qty === 0 ? '#f87171'
                                       : ($p['status'] === 'low_stock' ? '#fbbf24' : '#4ade80');
                        ?>
                        <div class="fs-3 fw-bold" style="color: <?= $qty_color ?>;">
                            <?= number_format($qty) ?>
                            <span class="fs-6 fw-normal text-white-50">units</span>
                        </div>
                    </div>

                    <hr style="border-color: rgba(255,255,255,.15);">

                    <div>
                        <div class="text-white-50 small mb-1">Workspace</div>
                        <div class="fw-semibold text-white">
                            <?= htmlspecialchars($p['workspace_id']) ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex flex-column gap-2 mt-4">
                        <a href="add_upd.php?id=<?= (int)$p['product_id'] ?>"
                           class="btn btn-light fw-semibold">
                            <i class="bi bi-pencil me-1"></i>Edit Product
                        </a>
                        <a href="del_prd.php?id=<?= (int)$p['product_id'] ?>"
                           class="btn btn-outline-light"
                           onclick="return confirm('Delete \'<?= addslashes(htmlspecialchars($p['product_name'])) ?>\'? This cannot be undone.')">
                            <i class="bi bi-trash me-1"></i>Delete Product
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Footer nav -->
    <div class="card-footer bg-transparent pt-0 pb-3 px-4">
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Products
        </a>
    </div>
</div>

<?php require_once 'footer.php'; ?>

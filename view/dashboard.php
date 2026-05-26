<?php
require_once __DIR__ . '/frame/header.php';

if (!isset($_SESSION['account_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once '../controller/prd/read.php';

$flash_success = '';
$flash_error   = '';
if (!empty($_SESSION['flash_success'])) {
    $flash_success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (!empty($_SESSION['flash_error'])) {
    $flash_error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
?>

<!-- ── Page Header ──────────────────────────────────────────── -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 page-title">
            <i class="bi bi-boxes me-2"></i>Product Inventory
        </h4>
        <p class="page-subtitle mb-0 mt-1">
            Workspace: <strong><?= htmlspecialchars($_SESSION['workspace_id']) ?></strong>
        </p>
    </div>
    <a href="add_upd.php" class="btn btn-brand">
        <i class="bi bi-plus-lg me-1"></i>Add Product
    </a>
</div>

<!-- ── Flash Alerts ─────────────────────────────────────────── -->
<?php if ($flash_success): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
        <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($flash_success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($flash_error): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($flash_error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- ── Filter Bar ────────────────────────────────────────────── -->
<div class="filter-bar mb-4">
    <form method="GET" action="dashboard.php" class="row g-2 align-items-end">

        <!-- Search -->
        <div class="col-12 col-md-4">
            <label class="form-label mb-1">Search</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="search"
                       placeholder="Product name or SKU…"
                       value="<?= htmlspecialchars($filters['search']) ?>">
            </div>
        </div>

        <!-- Category -->
        <div class="col-6 col-md-3">
            <label class="form-label mb-1">Category</label>
            <select class="form-select form-select-sm" name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"
                        <?= $filters['category'] === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Status -->
        <div class="col-6 col-md-2">
            <label class="form-label mb-1">Status</label>
            <select class="form-select form-select-sm" name="status">
                <option value="">All Statuses</option>
                <option value="active"    <?= $filters['status'] === 'active'    ? 'selected' : '' ?>>Active</option>
                <option value="inactive"  <?= $filters['status'] === 'inactive'  ? 'selected' : '' ?>>Inactive</option>
                <option value="low_stock" <?= $filters['status'] === 'low_stock' ? 'selected' : '' ?>>Low Stock</option>
            </select>
        </div>

        <!-- Actions -->
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-brand btn-sm px-3">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-x-lg me-1"></i>Clear
            </a>
        </div>

    </form>
</div>

<!-- ── Product Table ─────────────────────────────────────────── -->
<div class="card">
    <div class="card-header-custom d-flex align-items-center justify-content-between">
        <span class="fw-semibold">
            <i class="bi bi-table me-1"></i>Products
        </span>
        <span class="badge bg-white">
            <?= count($products) ?> result<?= count($products) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <div class="card-body p-0">
        <?php if (empty($products)): ?>
            <div class="text-center py-5 empty-state">
                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                No products found.
                <?php if (array_filter($filters)): ?>
                    <br><a href="dashboard.php" class="small">Clear filters</a>
                <?php else: ?>
                    <br>
                    <a href="add_upd.php" class="btn btn-brand btn-sm mt-2">
                        <i class="bi bi-plus-lg me-1"></i>Add your first product
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th>Status</th>
                            <th>Supplier</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $p): ?>
                        <?php
                            $qty = (int)$p['quantity'];
                            $qty_class = '';
                            if ($qty === 0)                  $qty_class = 'qty-zero';
                            elseif ($p['status'] === 'low_stock') $qty_class = 'qty-low';
                        ?>
                        <tr>
                            <td class="small" style="color: var(--text-muted);">
                                <?= htmlspecialchars($p['sku']) ?>
                            </td>
                            <td class="fw-semibold">
                                <a href="prd_view.php?id=<?= (int)$p['product_id'] ?>">
                                    <?= htmlspecialchars($p['product_name']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light">
                                    <?= htmlspecialchars($p['category']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="<?= $qty_class ?>">
                                    <?= number_format($qty) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                ₱<?= number_format((float)$p['unit_price'], 2) ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($p['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                </span>
                            </td>
                            <td class="small" style="color: var(--text-muted);">
                                <?= htmlspecialchars($p['supplier'] ?: '—') ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="prd_view.php?id=<?= (int)$p['product_id'] ?>"
                                       class="btn btn-sm btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="add_upd.php?id=<?= (int)$p['product_id'] ?>"
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="../controller/prd/del_prd.php?id=<?= (int)$p['product_id'] ?>"
                                       class="btn btn-sm btn-outline-danger" title="Delete"
                                       onclick="return confirm('Delete \'<?= addslashes(htmlspecialchars($p['product_name'])) ?>\'? This cannot be undone.')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/frame/footer.php'; ?>

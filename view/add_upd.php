<?php
require_once __DIR__ . '/frame/header.php';

if (!isset($_SESSION['account_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once '../model/product.php';

$workspace_id = $_SESSION['workspace_id'];
$product      = new Product();

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_update  = false;
$row        = [];

if ($product_id > 0) {
    $existing = $product->getById($product_id, $workspace_id);
    if ($existing) {
        $is_update = true;
        $row       = $existing;
    }
}

$error = '';
if (!empty($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
if (!empty($_SESSION['flash_old'])) {
    $row = array_merge($row, $_SESSION['flash_old']);
    unset($_SESSION['flash_old']);
}

$val = fn(string $key): string => htmlspecialchars($row[$key] ?? '');
$page_title = $is_update ? 'Edit Product' : 'Add New Product';
?>

<!-- ── Breadcrumb ────────────────────────────────────────────── -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Products</a></li>
        <?php if ($is_update): ?>
            <li class="breadcrumb-item">
                <a href="prd_view.php?id=<?= (int)$row['product_id'] ?>">
                    <?= htmlspecialchars($row['product_name']) ?>
                </a>
            </li>
        <?php endif; ?>
        <li class="breadcrumb-item active"><?= $is_update ? 'Edit' : 'Add New' ?></li>
    </ol>
</nav>

<div class="row justify-content-center">
<div class="col-12 col-lg-9 col-xl-8">

<div class="card">
    <div class="card-header-custom">
        <i class="bi bi-<?= $is_update ? 'pencil-square' : 'plus-circle' ?> me-2"></i>
        <?= $page_title ?>
    </div>

    <div class="card-body p-4">

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="../controller/prd/cre_upd_prd.php" method="POST" novalidate>
            <input type="hidden" name="product_id"
                   value="<?= $is_update ? (int)$row['product_id'] : '' ?>">

            <div class="row g-3">

                <!-- SKU -->
                <div class="col-12 col-md-4">
                    <label for="sku" class="form-label fw-semibold">
                        SKU <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="sku" name="sku"
                           placeholder="e.g. ELC-001"
                           value="<?= $val('sku') ?>" required>
                </div>

                <!-- Product Name -->
                <div class="col-12 col-md-8">
                    <label for="product_name" class="form-label fw-semibold">
                        Product Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="product_name" name="product_name"
                           placeholder="e.g. Mechanical Keyboard"
                           value="<?= $val('product_name') ?>" required>
                </div>

                <!-- Category -->
                <div class="col-12 col-md-6">
                    <label for="category" class="form-label fw-semibold">
                        Category <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="category" name="category"
                           placeholder="e.g. Peripherals"
                           value="<?= $val('category') ?>" required>
                </div>

                <!-- Supplier -->
                <div class="col-12 col-md-6">
                    <label for="supplier" class="form-label fw-semibold">Supplier</label>
                    <input type="text" class="form-control" id="supplier" name="supplier"
                           placeholder="e.g. Logitech PH"
                           value="<?= $val('supplier') ?>">
                </div>

                <!-- Quantity -->
                <div class="col-12 col-md-4">
                    <label for="quantity" class="form-label fw-semibold">
                        Quantity <span class="text-danger">*</span>
                    </label>
                    <input type="number" class="form-control" id="quantity" name="quantity"
                           min="0" placeholder="0"
                           value="<?= $val('quantity') ?>" required>
                </div>

                <!-- Unit Price -->
                <div class="col-12 col-md-4">
                    <label for="unit_price" class="form-label fw-semibold">
                        Unit Price (₱) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control" id="unit_price" name="unit_price"
                               min="0" step="0.01" placeholder="0.00"
                               value="<?= $val('unit_price') ?>" required>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-12 col-md-4">
                    <label for="status" class="form-label fw-semibold">
                        Status <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="status" name="status" required>
                        <?php
                        $statuses = ['active' => 'Active', 'inactive' => 'Inactive', 'low_stock' => 'Low Stock'];
                        $current  = $row['status'] ?? 'active';
                        foreach ($statuses as $sv => $sl):
                        ?>
                        <option value="<?= $sv ?>" <?= $current === $sv ? 'selected' : '' ?>>
                            <?= $sl ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">Description</label>
                    <textarea class="form-control" id="description" name="description"
                              rows="4" placeholder="Optional product description…"
                              ><?= $val('description') ?></textarea>
                </div>

            </div>

            <!-- Action buttons -->
            <div class="d-flex gap-2 mt-4 flex-wrap">
                <button type="submit" class="btn btn-brand px-4 fw-semibold">
                    <i class="bi bi-floppy me-1"></i>Save Changes
                </button>
                <a href="<?= $is_update ? 'prd_view.php?id=' . (int)$row['product_id'] : 'dashboard.php' ?>"
                   class="btn btn-outline-secondary px-4">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </a>
            </div>

        </form>
    </div>
</div>

</div>
</div>

<?php require_once __DIR__ . '/frame/footer.php'; ?>

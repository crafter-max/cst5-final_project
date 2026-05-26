<?php
// product.php — Product model.
// Handles all DB operations on the products table, always scoped to workspace_id.
//
// Suggested full products table schema:
//
//   CREATE TABLE products (
//       product_id   INT             NOT NULL AUTO_INCREMENT PRIMARY KEY,
//       workspace_id VARCHAR(50)     NOT NULL,
//       sku          VARCHAR(100)    NOT NULL,
//       product_name VARCHAR(150)    NOT NULL,
//       category     VARCHAR(100)    NOT NULL DEFAULT 'Uncategorized',
//       description  TEXT,
//       quantity     INT             NOT NULL DEFAULT 0,
//       unit_price   DECIMAL(10, 2)  NOT NULL DEFAULT 0.00, ----problematic in type railway
//       supplier     VARCHAR(150),
//       status       ENUM('active','inactive','low_stock') NOT NULL DEFAULT 'active', ----problematic in type railway
//       date_added   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
//       last_updated DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//       FOREIGN KEY (workspace_id) REFERENCES workspace(workspace_id)
//   );

require_once __DIR__ . '/db.php';

class Product {
    private PDO    $conn;
    private string $table = 'products';

    // Public properties mapped to table columns.
    public ?int    $product_id   = null;
    public string  $workspace_id = '';
    public string  $sku          = '';
    public string  $product_name = '';
    public string  $category     = '';
    public string  $description  = '';
    public int     $quantity     = 0;
    public float   $unit_price   = 0.00;
    public string  $supplier     = '';
    public string  $status       = 'active';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Fetch all products for a workspace, with optional filters.
     * Used by read.php (required by dashboard.php).
     *
     * $filters keys: 'search' (string), 'category' (string), 'status' (string)
     */
    public function getAll(string $workspace_id, array $filters = []): array {
        $sql = "SELECT * FROM {$this->table} WHERE workspace_id = :workspace_id";

        if (!empty($filters['category'])) {
            $sql .= " AND category = :category";
        }
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (product_name LIKE :search OR sku LIKE :search)";
        }

        $sql .= " ORDER BY date_added DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':workspace_id', $workspace_id);

        if (!empty($filters['category'])) {
            $stmt->bindParam(':category', $filters['category']);
        }
        if (!empty($filters['status'])) {
            $stmt->bindParam(':status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $search);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch a single product by product_id, scoped to workspace_id.
     * Returns false if not found or workspace mismatch.
     */
    public function getById(int $product_id, string $workspace_id): array|false {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table}
            WHERE product_id = :product_id AND workspace_id = :workspace_id
            LIMIT 1
        ");
        $stmt->bindParam(':product_id',   $product_id,   PDO::PARAM_INT);
        $stmt->bindParam(':workspace_id', $workspace_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Check if a product_id exists under a given workspace_id.
     * Used by cre_upd_prd.php to decide between INSERT and UPDATE.
     */
    public function exists(int $product_id, string $workspace_id): bool {
        $stmt = $this->conn->prepare("
            SELECT product_id FROM {$this->table}
            WHERE product_id = :product_id AND workspace_id = :workspace_id
            LIMIT 1
        ");
        $stmt->bindParam(':product_id',   $product_id,   PDO::PARAM_INT);
        $stmt->bindParam(':workspace_id', $workspace_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Insert a new product row using the current object's properties.
     */
    public function create(): bool {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
                (workspace_id, sku, product_name, category, description,
                 quantity, unit_price, supplier, status, date_added, last_updated)
            VALUES
                (:workspace_id, :sku, :product_name, :category, :description,
                 :quantity, :unit_price, :supplier, :status, NOW(), NOW())
        ");
        return $this->bindCoreFields($stmt);
    }

    /**
     * Update an existing product row identified by $this->product_id, scoped to $this->workspace_id.
     */
    public function update(): bool {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table}
            SET sku          = :sku,
                product_name = :product_name,
                category     = :category,
                description  = :description,
                quantity     = :quantity,
                unit_price   = :unit_price,
                supplier     = :supplier,
                status       = :status,
                last_updated = NOW()
            WHERE product_id   = :product_id
              AND workspace_id = :workspace_id
        ");
        $stmt->bindParam(':product_id', $this->product_id, PDO::PARAM_INT);
        return $this->bindCoreFields($stmt);
    }

    /**
     * Delete a product by product_id, scoped to workspace_id.
     */
    public function delete(int $product_id, string $workspace_id): bool {
        $stmt = $this->conn->prepare("
            DELETE FROM {$this->table}
            WHERE product_id = :product_id AND workspace_id = :workspace_id
        ");
        $stmt->bindParam(':product_id',   $product_id,   PDO::PARAM_INT);
        $stmt->bindParam(':workspace_id', $workspace_id);
        return $stmt->execute();
    }

    /**
     * Get a distinct list of categories for a workspace.
     * Drives the category filter dropdown in dashboard.php.
     */
    public function getCategories(string $workspace_id): array {
        $stmt = $this->conn->prepare("
            SELECT DISTINCT category FROM {$this->table}
            WHERE workspace_id = :workspace_id
            ORDER BY category ASC
        ");
        $stmt->bindParam(':workspace_id', $workspace_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Bind the shared core field set used by both create() and update().
     */
    private function bindCoreFields(PDOStatement $stmt): bool {
        $stmt->bindParam(':workspace_id', $this->workspace_id);
        $stmt->bindParam(':sku',          $this->sku);
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':category',     $this->category);
        $stmt->bindParam(':description',  $this->description);
        $stmt->bindParam(':quantity',     $this->quantity,    PDO::PARAM_INT);
        $stmt->bindParam(':unit_price',   $this->unit_price);
        $stmt->bindParam(':supplier',     $this->supplier);
        $stmt->bindParam(':status',       $this->status);
        return $stmt->execute();
    }
}

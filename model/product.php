<?php
// Product model — handles all database operations for the products table, scoped to workspace_id.

require_once __DIR__ . '/db.php';

class Product {
    private PDO    $conn;
    private string $table = 'products';

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
        $this->conn = (new Database())->getConnection();
    }

    /**
     * Returns all products for a workspace with optional filters.
     * Accepted filter keys: search, category, status.
     */
    public function getAll(string $workspace_id, array $filters = []): array {
        $sql = "SELECT * FROM {$this->table} WHERE workspace_id = :workspace_id";

        if (!empty($filters['category'])) $sql .= " AND category = :category";
        if (!empty($filters['status']))   $sql .= " AND status = :status";
        if (!empty($filters['search']))   $sql .= " AND (product_name LIKE :search OR sku LIKE :search)";

        $sql .= " ORDER BY date_added DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':workspace_id', $workspace_id);

        if (!empty($filters['category'])) $stmt->bindParam(':category', $filters['category']);
        if (!empty($filters['status']))   $stmt->bindParam(':status',   $filters['status']);
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $search);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Returns a single product row by ID, scoped to workspace_id. Returns false if not found. */
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

    /** Returns true if a product exists under the given workspace_id. */
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

    /** Inserts a new product row from the current object's properties. */
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

    /** Updates an existing product row identified by product_id, scoped to workspace_id. */
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

    /** Deletes a product by ID, scoped to workspace_id. */
    public function delete(int $product_id, string $workspace_id): bool {
        $stmt = $this->conn->prepare("
            DELETE FROM {$this->table}
            WHERE product_id = :product_id AND workspace_id = :workspace_id
        ");
        $stmt->bindParam(':product_id',   $product_id,   PDO::PARAM_INT);
        $stmt->bindParam(':workspace_id', $workspace_id);
        return $stmt->execute();
    }

    /** Returns a distinct list of categories for a workspace, used by dashboard filters. */
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

    /** Binds the shared field set used by both create() and update(). */
    private function bindCoreFields(PDOStatement $stmt): bool {
        $stmt->bindParam(':workspace_id', $this->workspace_id);
        $stmt->bindParam(':sku',          $this->sku);
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':category',     $this->category);
        $stmt->bindParam(':description',  $this->description);
        $stmt->bindParam(':quantity',     $this->quantity,   PDO::PARAM_INT);
        $stmt->bindParam(':unit_price',   $this->unit_price);
        $stmt->bindParam(':supplier',     $this->supplier);
        $stmt->bindParam(':status',       $this->status);
        return $stmt->execute();
    }
}

<?php
// account.php — Account model.
// Handles all DB operations related to the accounts table.
//
// NOTE: Your accounts table schema should include a `password` (VARCHAR 255) column
// to support login. Suggested full schema:
//
//   CREATE TABLE accounts (
//       account_id   INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
//       workspace_id VARCHAR(50)  NOT NULL,
//       first_name   VARCHAR(100) NOT NULL,
//       last_name    VARCHAR(100) NOT NULL,
//       contact      VARCHAR(20)  NOT NULL,
//       email        VARCHAR(150) NOT NULL UNIQUE,
//       hire_date    DATE         NOT NULL,
//       password     VARCHAR(255) NOT NULL,
//       FOREIGN KEY (workspace_id) REFERENCES workspace(workspace_id)
//   );

require_once __DIR__ . '/db.php';

class Account {
    private PDO    $conn;
    private string $table = 'accounts';

    // Public properties mapped to table columns.
    public ?int    $account_id   = null;
    public string  $workspace_id = '';
    public string  $first_name   = '';
    public string  $last_name    = '';
    public string  $contact      = '';
    public string  $email        = '';
    public string  $hire_date    = '';
    public string  $password     = ''; // plain-text on input; always stored hashed.

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Fetch a full account row by email address.
     * Used by login.php to validate credentials.
     */
    public function findByEmail(string $email): array|false {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1"
        );
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Check whether an email is already registered.
     * Used during registration to prevent duplicates.
     */
    public function emailExists(string $email): bool {
        $stmt = $this->conn->prepare(
            "SELECT account_id FROM {$this->table} WHERE email = :email LIMIT 1"
        );
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Verify that a workspace_id exists in the workspace table.
     * Required before creating an account — workspace_id is not auto-generated.
     */
    public function workspaceExists(string $workspace_id): bool {
        $stmt = $this->conn->prepare(
            "SELECT workspace_id FROM workspace WHERE workspace_id = :workspace_id LIMIT 1"
        );
        $stmt->bindParam(':workspace_id', $workspace_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Insert a new account row.
     * Hashes $this->password with bcrypt before storing.
     * Returns true on success.
     */
    public function create(): bool {
        $hashed = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
                (workspace_id, first_name, last_name, contact, email, hire_date, password)
            VALUES
                (:workspace_id, :first_name, :last_name, :contact, :email, :hire_date, :password)
        ");
        $stmt->bindParam(':workspace_id', $this->workspace_id);
        $stmt->bindParam(':first_name',   $this->first_name);
        $stmt->bindParam(':last_name',    $this->last_name);
        $stmt->bindParam(':contact',      $this->contact);
        $stmt->bindParam(':email',        $this->email);
        $stmt->bindParam(':hire_date',    $this->hire_date);
        $stmt->bindParam(':password',     $hashed);
        return $stmt->execute();
    }
}

<?php
// db.php — Database connection model.
// Provides a single PDO connection instance for the entire runtime.
// All model files (account.php, product.php) instantiate this to get a connection.

class Database {
    private string $host     = 'localhost';
    private string $db_name  = 'invSys';
    private string $username = 'root';
    private string $password = '';
    private ?PDO   $conn     = null;

    /**
     * Returns the active PDO connection, creating it on first call.
     */
    public function getConnection(): PDO {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
                $this->conn = new PDO($dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);
            } catch (PDOException $e) {
                // In production, log this and show a generic error page instead.
                die(json_encode(['db_error' => $e->getMessage()]));
            }
        }
        return $this->conn;
    }
}

<?php

class Database {
    private string $host;
    private int    $port;
    private string $db_name;
    private string $username;
    private string $password;
    private ?PDO   $conn = null;

    public function __construct() {
        $this->loadEnv(__DIR__ . '/.env');

        // Covers FrankenPHP ($_SERVER), Railway standard (getenv), and local (.env/$_ENV)
        $this->host     = $_SERVER['DB_HOST'] ?? (getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost'));
        $this->port     = (int)($_SERVER['DB_PORT'] ?? (getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? 3306)));
        $this->db_name  = $_SERVER['DB_NAME'] ?? (getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? ''));
        $this->username = $_SERVER['DB_USER'] ?? (getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root'));
        $this->password = $_SERVER['DB_PASS'] ?? (getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? ''));
    }

    /**
     * Returns the active PDO connection, creating it on first call.
     */
    public function getConnection(): PDO {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
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

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Parse a .env file and populate $_ENV.
     * Supports: KEY=value, KEY="value", KEY='value', and # comments.
     * Skips blank lines and lines that start with #.
     */
    private function loadEnv(string $path): void {
        if (!file_exists($path)) {
            return; // .env is optional; fall back to defaults or server env vars.
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments and blank lines.
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Must contain an = sign.
            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            // Strip surrounding quotes if present.
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            // Only set if not already defined by the server environment.
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key]    = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

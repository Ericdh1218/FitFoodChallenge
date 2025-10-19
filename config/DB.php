<?php
namespace App\Config;

use PDO;
use PDOException;

class DB {
    private static ?PDO $pdo = null;

    public static function pdo(): PDO {
        if (self::$pdo) return self::$pdo;

        $host = getenv('DB_HOST') ?: 'localhost';
        $db   = getenv('DB_NAME') ?: 'fitfood_db';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';

        $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            self::$pdo = new PDO($dsn, $user, $pass, $opt);
        } catch (PDOException $e) {
            die('DB Connection failed: ' . $e->getMessage());
        }
        return self::$pdo;
    }
}

<?php
// config/config.php - Database connection (PDO)
declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class DB {
    public static function pdo(): PDO {
        static $pdo = null;
        if ($pdo) return $pdo;

        $dsn = sprintf('%s:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_DRIVER') ?: 'mysql',
            getenv('DB_HOST') ?: '127.0.0.1',
            getenv('DB_PORT') ?: '3306',
            getenv('DB_NAME') ?: 'fitfoodchallenge'
        );

        try {
            $pdo = new PDO($dsn, getenv('DB_USER') ?: 'root', getenv('DB_PASS') ?: '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo 'Error de conexión a la base de datos.';
            exit;
        }
        return $pdo;
    }
}
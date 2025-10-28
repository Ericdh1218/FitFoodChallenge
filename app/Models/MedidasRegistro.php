<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class MedidasRegistro
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DB::conn();
    }

    /**
     * Crea un nuevo registro de medida (peso) para un usuario.
     */
    public function create(int $userId, string $fecha, float $peso): bool
    {
        $sql = "INSERT INTO medidas_registro (user_id, fecha, peso)
                VALUES (:user_id, :fecha, :peso)";

        $st = $this->pdo->prepare($sql);

        return $st->execute([
            ':user_id' => $userId,
            ':fecha'   => $fecha,
            ':peso'    => $peso
        ]);
    }

    /**
     * Obtiene los últimos N registros de peso para un usuario.
     */
    public function getRecentHistory(int $userId, int $limit = 5): array
    {
        $sql = "SELECT fecha, peso
                FROM medidas_registro
                WHERE user_id = :user_id
                ORDER BY fecha DESC
                LIMIT :limit";
        $st = $this->pdo->prepare($sql);
        $lim = $limit;
        $st->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $st->bindParam(':limit', $lim, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    // En MedidasRegistro.php

// ... (constructor) ...

// REEMPLAZA create() CON ESTO:
/**
     * Crea o actualiza un registro de medida para un día específico.
     */
    public function saveOrUpdateByDate(int $userId, string $fecha, float $peso): bool
    {
        // Esta consulta usa 3 placeholders: :user_id, :fecha, :peso
        $sql = "INSERT INTO medidas_registro (user_id, fecha, peso)
                VALUES (:user_id, :fecha, :peso)
                ON DUPLICATE KEY UPDATE
                    peso = VALUES(peso)"; // <-- CORRECCIÓN: Usa VALUES(peso)
        
        $st = $this->pdo->prepare($sql);
        
        // El array de execute() ahora coincide con los 3 placeholders
        return $st->execute([
            ':user_id' => $userId,
            ':fecha'   => $fecha,
            ':peso'    => $peso
        ]);
    }

// ... (getRecentHistory() se queda igual) ...

// AÑADE ESTA FUNCIÓN:
public function getHistoryForChart(int $userId): array
{
    $sql = "SELECT fecha, peso 
            FROM medidas_registro 
            WHERE user_id = :user_id 
            ORDER BY fecha ASC"; // <-- ASCENDENTE para gráficas
    $st = $this->pdo->prepare($sql);
    $st->execute([':user_id' => $userId]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}
}
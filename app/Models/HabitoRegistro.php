<?php
namespace App\Models;

use App\Config\DB;

use App\Models\Trivia;
use PDO;

class HabitoRegistro
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DB::conn();
    }

    /**
     * Guarda el check-in de hábitos de un usuario para un día específico.
     * Usa ON DUPLICATE KEY UPDATE para evitar errores si ya existe.
     */
    public function saveCheckin(int $userId, string $fecha, array $data): bool
    {
        // ==========================================================
        // ===                 CORRECCIÓN AQUÍ                  ===
        // ==========================================================
        
        // 1. Damos nombres únicos a los placeholders del UPDATE
        //    (ej. :agua_update)
        $sql = "INSERT INTO habitos_registro 
                    (user_id, fecha, agua_cumplido, sueno_cumplido, entrenamiento_cumplido)
                VALUES 
                    (:user_id, :fecha, :agua, :sueno, :entrenamiento)
                ON DUPLICATE KEY UPDATE
                    agua_cumplido = :agua_update,
                    sueno_cumplido = :sueno_update,
                    entrenamiento_cumplido = :entrenamiento_update";
        
        $st = $this->pdo->prepare($sql);
        
        // 2. Pasamos todos los parámetros que la consulta espera.
        //    Ahora la consulta tiene 8 placeholders y le pasamos 8 valores.
        return $st->execute([
            ':user_id'       => $userId,
            ':fecha'         => $fecha,
            ':agua'          => $data['agua_cumplido'],
            ':sueno'         => $data['sueno_cumplido'],
            ':entrenamiento' => $data['entrenamiento_cumplido'],
            // --- Parámetros duplicados para la sección UPDATE ---
            ':agua_update'   => $data['agua_cumplido'],
            ':sueno_update'  => $data['sueno_cumplido'],
            ':entrenamiento_update' => $data['entrenamiento_cumplido']
        ]);
    }

    public function findByDate(int $userId, string $fecha): ?array
    {
        $sql = "SELECT * FROM habitos_registro WHERE user_id = :user_id AND fecha = :fecha";
        $st = $this->pdo->prepare($sql);
        $st->execute([':user_id' => $userId, ':fecha' => $fecha]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Obtiene los últimos N registros de hábitos para un usuario.
     */
    public function getRecentHistory(int $userId, int $limit = 5): array
    {
        $sql = "SELECT fecha, agua_cumplido, sueno_cumplido, entrenamiento_cumplido 
                FROM habitos_registro 
                WHERE user_id = :user_id 
                ORDER BY fecha DESC 
                LIMIT :limit";
        $st = $this->pdo->prepare($sql);
        // bindParam necesita variables, por eso asignamos $limit a una var local
        $lim = $limit; 
        $st->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $st->bindParam(':limit', $lim, PDO::PARAM_INT); 
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    // En HabitoRegistro.php

// ... (constructor, findByDate, getRecentHistory, saveCheckin) ...

// AÑADE ESTA FUNCIÓN:
public function getHistoryForChart(int $userId, int $days = 30): array
{
    // Suma los hábitos cumplidos por día
    $sql = "SELECT 
                fecha, 
                SUM(agua_cumplido) as agua, 
                SUM(sueno_cumplido) as sueno, 
                SUM(entrenamiento_cumplido) as entrenamiento
            FROM habitos_registro
            WHERE user_id = :user_id AND fecha >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY fecha
            ORDER BY fecha ASC";
    $st = $this->pdo->prepare($sql);
    $st->execute([':user_id' => $userId, ':days' => $days]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}
}
<?php
namespace App\Models;

use App\Config\DB;
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
}
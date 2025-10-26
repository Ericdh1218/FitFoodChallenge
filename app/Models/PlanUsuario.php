<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class PlanUsuario
{
    protected $pdo;

    public function __construct() { $this->pdo = DB::conn(); }
    public function startOrGetPlan(int $userId, int $areaId): ?array
    {
        // 1. Busca si YA EXISTE un plan (activo o completado)
        $sqlSelect = "SELECT * FROM planes_usuarios WHERE user_id = :user_id AND area_id = :area_id";
        $stSelect = $this->pdo->prepare($sqlSelect);
        $stSelect->execute([':user_id' => $userId, ':area_id' => $areaId]);
        $existingPlan = $stSelect->fetch(PDO::FETCH_ASSOC);

        // 2. Si ya existe...
        if ($existingPlan) {
            // ...y está COMPLETADO...
            if ($existingPlan['completado'] == 1) {
                // === ¡NUEVA LÓGICA: RESETEAR! ===
                // Lo volvemos a poner en paso 1, no completado, y actualizamos fecha
                $sqlReset = "UPDATE planes_usuarios 
                             SET paso_actual = 1, completado = 0, fecha_actualizacion = CURRENT_TIMESTAMP 
                             WHERE id = :id";
                $stReset = $this->pdo->prepare($sqlReset);
                $resetSuccess = $stReset->execute([':id' => $existingPlan['id']]);

                if ($resetSuccess) {
                    // Si el reseteo funcionó, volvemos a buscar los datos actualizados
                    $stSelect->execute([':user_id' => $userId, ':area_id' => $areaId]);
                    return $stSelect->fetch(PDO::FETCH_ASSOC); // Devuelve el plan reseteado
                } else {
                    // Error al resetear, devuelve el plan completado original
                    error_log("Error al resetear plan completado ID: " . $existingPlan['id']);
                    return $existingPlan; 
                }
                // ==============================
            } else {
                // ...y NO está completado (está activo), simplemente devuélvelo
                return $existingPlan;
            }
        }

        // 3. Si NO existe, intenta INSERTAR uno nuevo (igual que antes)
        $sqlInsert = "INSERT INTO planes_usuarios (user_id, area_id, paso_actual, completado)
                      VALUES (:user_id, :area_id, 1, 0)";
        $stInsert = $this->pdo->prepare($sqlInsert);
        $success = $stInsert->execute([':user_id' => $userId, ':area_id' => $areaId]);

        if ($success) {
            // 4. Si la inserción fue exitosa, busca y devuelve el plan recién creado
             $stSelect->execute([':user_id' => $userId, ':area_id' => $areaId]);
             return $stSelect->fetch(PDO::FETCH_ASSOC);
        } else {
            // 5. Si la inserción falló... (igual que antes)
            error_log("Error al insertar nuevo plan para user $userId, area $areaId. Intentando buscar de nuevo.");
            $stSelect->execute([':user_id' => $userId, ':area_id' => $areaId]);
            return $stSelect->fetch(PDO::FETCH_ASSOC); 
        }
    }

    /**
     * Busca un plan ACTIVO para un usuario y área específicos.
     * (Esta función se queda igual, pero ahora startOrGetPlan no la usa directamente al inicio)
     */
    public function findActiveByUserAndArea(int $userId, int $areaId): ?array
    {
        $sql = "SELECT * FROM planes_usuarios
                WHERE user_id = :user_id AND area_id = :area_id AND completado = 0";
        $st = $this->pdo->prepare($sql);
        $st->execute([':user_id' => $userId, ':area_id' => $areaId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Avanza al siguiente paso del plan.
     * Marca como completado si llega al último paso (asumimos 3 pasos).
     */
    public function advanceStep(int $planId, int $currentStep): bool
    {
        $nextStep = $currentStep + 1;
        $isCompleted = ($nextStep > 3) ? 1 : 0; // Marca completado si pasa del paso 3

        if ($isCompleted) {
             $sql = "UPDATE planes_usuarios SET completado = 1, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = :id";
             $st = $this->pdo->prepare($sql);
             return $st->execute([':id' => $planId]);
        } else {
             $sql = "UPDATE planes_usuarios SET paso_actual = :next_step, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = :id";
             $st = $this->pdo->prepare($sql);
             return $st->execute([':next_step' => $nextStep, ':id' => $planId]);
        }
    }
}
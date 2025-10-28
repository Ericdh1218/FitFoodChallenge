<?php
namespace App\Models;
use App\Models\User;
use App\Config\DB;
use PDO;

class Actividad
{
    protected $pdo;

    public function __construct() { $this->pdo = DB::conn(); }

    /**
     * Obtiene N actividades NEAT aleatorias.
     */
    public function getNeatActivities(int $excludeId = null): array
    {
        // === CAMBIO: Añadido 'icono' y 'id' al SELECT ===
        $sql = "SELECT id, titulo, descripcion
                FROM actividades_neat";
        
        $params = [];
        if ($excludeId) {
            $sql .= " WHERE id != :exclude_id"; // Excluir la que ya salió
            $params[':exclude_id'] = $excludeId;
        }
        
        $sql .= " ORDER BY titulo ASC";
        
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * ==========================================
     * NUEVO MÉTODO: Obtiene UNA actividad aleatoria
     * ==========================================
     */
    public function getOneRandomActivity(): ?array
    {
        $sql = "SELECT id, titulo, descripcion
                FROM actividades_neat 
                ORDER BY RAND() 
                LIMIT 1";
        $st = $this->pdo->query($sql);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function completarActividad(int $userId, int $actividadId): ?int
    {
        $xpGanado = 5; // Puntos por defecto

        // 1. Registra la actividad
        $sqlAct = "INSERT INTO actividades_neat_registro (user_id, actividad_id, xp_ganado) 
                   VALUES (:uid, :aid, :xp)";
        $stAct = $this->pdo->prepare($sqlAct);
        $stAct->execute([
            ':uid' => $userId,
            ':aid' => $actividadId,
            ':xp'  => $xpGanado
        ]);

        // 2. Actualiza el XP total del usuario (usando el modelo User)
        $userModel = new User(); // Asegúrate de tener 'use App\Models\User;' al inicio de Actividad.php
        $userModel->addXp($userId, $xpGanado);

        // 3. Devuelve los puntos ganados
        return $xpGanado;
    }

    /**
     * ==========================================
     * NUEVO: Obtiene los conteos de completados por actividad
     * ==========================================
     */
    public function getCompletionCounts(int $userId): array
    {
        $sql = "SELECT actividad_id, COUNT(*) as total_completado
                FROM actividades_neat_registro
                WHERE user_id = :user_id
                GROUP BY actividad_id";
        
        $st = $this->pdo->prepare($sql);
        $st->execute([':user_id' => $userId]);
        
        // Devuelve un array asociativo [actividad_id => total]
        return $st->fetchAll(PDO::FETCH_KEY_PAIR); 
    }
}

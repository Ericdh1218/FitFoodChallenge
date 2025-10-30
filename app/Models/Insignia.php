<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class Insignia
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DB::conn();
    }

    /**
     * Busca todas las insignias que ha ganado un usuario específico.
     * Hace un JOIN con la tabla 'insignias' para obtener los detalles.
     */
    public function findInsigniasByUserId(int $userId): array
    {
        $sql = "SELECT 
                    i.nombre, 
                    i.descripcion, 
                    i.icono_url, 
                    iu.fecha_obtenida
                FROM insignias_usuarios iu
                JOIN insignias i ON iu.insignia_id = i.id
                WHERE iu.user_id = :user_id
                ORDER BY iu.fecha_obtenida DESC"; // Mostrar las más recientes primero
        
        $st = $this->pdo->prepare($sql);
        $st->execute([':user_id' => $userId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
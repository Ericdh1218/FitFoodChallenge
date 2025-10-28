<?php
namespace App\Models;
use App\Config\DB;
use PDO;

class UsuarioEstado
{
    protected $pdo;
    public function __construct() { $this->pdo = DB::conn(); }

    /** Obtiene o crea el estado de hoy para un usuario */
    public function getOrCreateHoy(int $userId): array
    {
        $fechaHoy = date('Y-m-d');
        // 1. Intenta buscar
        $st = $this->pdo->prepare("SELECT * FROM usuario_estado_diario WHERE user_id = :uid AND fecha = :fecha");
        $st->execute([':uid' => $userId, ':fecha' => $fechaHoy]);
        $estado = $st->fetch(PDO::FETCH_ASSOC);

        if ($estado) {
            return $estado; // Devuelve el estado de hoy
        }
        
        // 2. Si no existe, lo crea
        $stInsert = $this->pdo->prepare("INSERT INTO usuario_estado_diario (user_id, fecha, refrescos_neat_usados) VALUES (:uid, :fecha, 0)");
        $stInsert->execute([':uid' => $userId, ':fecha' => $fechaHoy]);
        
        // Devuelve el estado recién creado
        return [
            'user_id' => $userId,
            'fecha' => $fechaHoy,
            'refrescos_neat_usados' => 0
        ];
    }
    
    /** Incrementa el contador de refrescos usados hoy */
    public function usarRefresco(int $userId): bool
    {
        $fechaHoy = date('Y-m-d');
        $sql = "UPDATE usuario_estado_diario 
                SET refrescos_neat_usados = refrescos_neat_usados + 1 
                WHERE user_id = :uid AND fecha = :fecha";
        $st = $this->pdo->prepare($sql);
        $st->execute([':uid' => $userId, ':fecha' => $fechaHoy]);
        return $st->rowCount() > 0;
    }
}
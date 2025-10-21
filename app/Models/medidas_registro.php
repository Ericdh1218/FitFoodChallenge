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
}
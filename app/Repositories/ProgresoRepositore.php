<?php
namespace App\Repositories;

use App\Config\DB;
use PDO;

class ProgresoRepository
{
    private PDO $pdo;
    public function __construct() { $this->pdo = \App\Config\DB::pdo(); }

    // Requiere índice único (user_id, date) en la tabla 'progress'
    public function upsertDaily(int $user, int $minutes, int $water): bool
    {
        $sql = "INSERT INTO progress (user_id, date, minutes_active, water_glasses)
                VALUES (:u, CURDATE(), :m, :w)
                ON DUPLICATE KEY UPDATE
                  minutes_active = minutes_active + VALUES(minutes_active),
                  water_glasses  = water_glasses  + VALUES(water_glasses)";
        $st = $this->pdo->prepare($sql);
        return $st->execute([':u'=>$user, ':m'=>$minutes, ':w'=>$water]);
    }
}

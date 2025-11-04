<?php
namespace App\Models;

use App\Config\DB;
use PDO;
use DateInterval;
use DateTimeImmutable;

class Progreso
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DB::conn();
    }

    /** Stats de HOY para el usuario */
    public function getHoy(int $userId): array
    {
        $sql = "SELECT min_actividad, vasos_agua, completado
                FROM progreso_diario
                WHERE user_id = :uid AND fecha = CURDATE()";
        $st = $this->pdo->prepare($sql);
        $st->execute([':uid' => $userId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: ['min_actividad' => 0, 'vasos_agua' => 0, 'completado' => 0];
    }

    /**
     * Racha de días consecutivos hasta HOY.
     * Cuenta un día como “ok” si:
     *   min_actividad > 0  OR  vasos_agua > 0  OR  completado = 1
     */
    public function getRacha(int $userId, int $maxDias = 60): int
    {
        $sql = "SELECT fecha,
                       (COALESCE(min_actividad,0) > 0
                        OR COALESCE(vasos_agua,0) > 0
                        OR COALESCE(completado,0) = 1) AS ok
                FROM progreso_diario
                WHERE user_id = :uid AND fecha <= CURDATE()
                ORDER BY fecha DESC
                LIMIT :lim";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(':uid', $userId, PDO::PARAM_INT);
        $st->bindValue(':lim', $maxDias, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        $streak   = 0;
        $expected = new DateTimeImmutable(date('Y-m-d')); // hoy

        foreach ($rows as $r) {
            $day = new DateTimeImmutable($r['fecha']);
            // si la fila corresponde al día esperado…
            if ($day->format('Y-m-d') === $expected->format('Y-m-d')) {
                if ((int)$r['ok'] === 1) {
                    $streak++;
                    $expected = $expected->sub(new DateInterval('P1D'));
                } else {
                    break; // día esperado pero “no ok” → corta racha
                }
            } else {
                break;   // falta ese día → corta racha
            }
        }
        return $streak;
    }

    /** “Upsert” simplito para escribir HOY (útil si aún no tienes endpoints) */
    public function upsertHoy(int $userId, int $min, int $agua, int $completado = 0): bool
    {
        $sql = "INSERT INTO progreso_diario (user_id, fecha, min_actividad, vasos_agua, completado)
                VALUES (:uid, CURDATE(), :min, :agua, :comp)
                ON DUPLICATE KEY UPDATE
                min_actividad = VALUES(min_actividad),
                vasos_agua    = VALUES(vasos_agua),
                completado    = VALUES(completado)";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            ':uid'  => $userId,
            ':min'  => $min,
            ':agua' => $agua,
            ':comp' => $completado
        ]);
    }

    /** Incrementa hoy y marca completado=1 si hay algo */
    public function addHoy(int $userId, int $deltaMin, int $deltaAgua): array
{
    $st = $this->pdo->prepare("
        INSERT INTO progreso_diario (user_id, fecha, min_actividad, vasos_agua, completado)
        VALUES (:uid, CURDATE(), :min, :agua, :comp)
        ON DUPLICATE KEY UPDATE 
            min_actividad = min_actividad + VALUES(min_actividad),
            vasos_agua    = vasos_agua    + VALUES(vasos_agua),
            completado    = GREATEST(completado, VALUES(completado))
    ");
    $st->execute([
        ':uid'  => $userId,
        ':min'  => max(0,$deltaMin),
        ':agua' => max(0,$deltaAgua),
        ':comp' => ($deltaMin>0 || $deltaAgua>0) ? 1 : 0,
    ]);

    $hoy   = $this->getHoy($userId);
    $racha = $this->getRacha($userId);
    return ['hoy'=>$hoy,'racha'=>$racha];
}

}

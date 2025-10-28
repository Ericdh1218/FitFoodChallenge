<?php
namespace App\Models;
use App\Config\DB;
use PDO;

class ProgresoFoto
{
    protected $pdo;
    public function __construct() { $this->pdo = DB::conn(); }

    public function create(int $userId, string $fecha, string $nombreArchivo, ?string $nota): bool
    {
        $sql = "INSERT INTO progreso_fotos (user_id, fecha_subida, nombre_archivo, nota)
                VALUES (:uid, :fecha, :nombre, :nota)";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            ':uid' => $userId,
            ':fecha' => $fecha,
            ':nombre' => $nombreArchivo,
            ':nota' => $nota
        ]);
    }

    public function findByUserId(int $userId): array
    {
        $st = $this->pdo->prepare("SELECT * FROM progreso_fotos WHERE user_id = :uid ORDER BY fecha_subida DESC");
        $st->execute([':uid' => $userId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    // ... (constructor y create existentes) ...

    /**
     * Busca una foto por su ID y verifica que pertenezca al usuario.
     */
    public function findByIdAndUser(int $fotoId, int $userId): ?array
    {
        $st = $this->pdo->prepare(
            "SELECT * FROM progreso_fotos WHERE id = :id AND user_id = :user_id"
        );
        $st->execute([':id' => $fotoId, ':user_id' => $userId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Elimina un registro de foto por su ID.
     */
    public function deleteById(int $fotoId): bool
    {
        $st = $this->pdo->prepare("DELETE FROM progreso_fotos WHERE id = :id");
        return $st->execute([':id' => $fotoId]);
    }
}
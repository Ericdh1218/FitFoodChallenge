<?php
namespace App\Models;
use App\Config\DB;
use PDO;

class Desafio
{
    protected $pdo;
    public function __construct() { $this->pdo = DB::conn(); }

    public function getAll(): array
    {
        return $this->pdo->query("SELECT * FROM desafios")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByCodigo(string $codigo): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM desafios WHERE codigo = :codigo");
        $st->execute([':codigo' => $codigo]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

     public function find(int $id): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM desafios WHERE id=:id");
        $st->execute([':id' => $id]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    /** Crea un desafío */
    public function create(array $d): bool
    {
        $sql = "INSERT INTO desafios (codigo, titulo, descripcion, duracion_dias, recompensa_xp, tipo_habito_link)
                VALUES (:codigo, :titulo, :descripcion, :duracion_dias, :recompensa_xp, :tipo_habito_link)";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            ':codigo'          => trim($d['codigo']),
            ':titulo'          => trim($d['titulo']),
            ':descripcion'     => $d['descripcion'] ?? null,
            ':duracion_dias'   => (int)($d['duracion_dias'] ?? 1),
            ':recompensa_xp'   => (int)($d['recompensa_xp'] ?? 50),
            ':tipo_habito_link'=> $d['tipo_habito_link'] ?: null,
        ]);
    }

    /** Actualiza un desafío */
    public function update(int $id, array $d): bool
    {
        $sql = "UPDATE desafios SET
                    codigo = :codigo,
                    titulo = :titulo,
                    descripcion = :descripcion,
                    duracion_dias = :duracion_dias,
                    recompensa_xp = :recompensa_xp,
                    tipo_habito_link = :tipo_habito_link
                WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            ':codigo'          => trim($d['codigo']),
            ':titulo'          => trim($d['titulo']),
            ':descripcion'     => $d['descripcion'] ?? null,
            ':duracion_dias'   => (int)($d['duracion_dias'] ?? 1),
            ':recompensa_xp'   => (int)($d['recompensa_xp'] ?? 50),
            ':tipo_habito_link'=> $d['tipo_habito_link'] ?: null,
            ':id'              => $id,
        ]);
    }

    /** Elimina un desafío */
    public function delete(int $id): bool
    {
        // Si tienes FK con ON DELETE CASCADE en desafios_usuarios no hace falta borrar manualmente
        $st = $this->pdo->prepare("DELETE FROM desafios WHERE id=:id");
        return $st->execute([':id' => $id]);
    }
}
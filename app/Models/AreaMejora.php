<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class AreaMejora
{
    protected $pdo;

    public function __construct() { $this->pdo = DB::conn(); }

    /** Obtiene todas las áreas de mejora disponibles */
    public function getAll(): array
    {
        $sql = "SELECT id, codigo, titulo, descripcion_corta, icono FROM areas_mejora ORDER BY id ASC";
        $st = $this->pdo->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Busca un área por su código (ej: 'desayuno') */
    public function findByCode(string $codigo): ?array
    {
        $sql = "SELECT * FROM areas_mejora WHERE codigo = :codigo";
        $st = $this->pdo->prepare($sql);
        $st->execute([':codigo' => $codigo]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
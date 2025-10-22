<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class Rutina
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DB::conn();
    }

    /**
     * Obtiene todas las rutinas prediseñadas
     */
    public function getPredefinidas(): array
    {
        $st = $this->pdo->query(
            "SELECT id, nombre_rutina, descripcion, nivel 
             FROM rutinas_prediseñadas 
             ORDER BY nivel"
        );
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca una rutina prediseñada por su ID
     */
    public function findPredefinida(int $id): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM rutinas_prediseñadas WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Obtiene todos los ejercicios de una rutina prediseñada, 
     * incluyendo las series y repeticiones.
     */
    public function getEjerciciosDeRutinaPredefinida(int $rutina_id): array
    {
        $sql = "SELECT e.id, e.nombre, rpe.series_reps
                FROM ejercicios e
                JOIN rutina_prediseñada_ejercicios rpe ON e.id = rpe.ejercicio_id
                WHERE rpe.rutina_id = :rutina_id";
        
        $st = $this->pdo->prepare($sql);
        $st->execute([':rutina_id' => $rutina_id]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
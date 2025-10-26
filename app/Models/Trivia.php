<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class Trivia
{
    protected $pdo;

    public function __construct() { $this->pdo = DB::conn(); }

    /**
     * Obtiene una pregunta aleatoria de una categoría específica.
     */
    public function getRandomQuestion($categorias): ?array
    {
        // Asegura que $categorias sea un array
        if (!is_array($categorias)) {
            $categorias = [$categorias];
        }

        if (empty($categorias)) {
            return null;
        }

        // Crea los placeholders (?, ?, ?) para el IN
        $placeholders = implode(',', array_fill(0, count($categorias), '?'));

        $sql = "SELECT id, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, 
                       respuesta_correcta, feedback_correcto, feedback_incorrecto
                FROM quiz_preguntas
                WHERE categoria IN ({$placeholders}) -- Usa IN (...)
                ORDER BY RAND()
                LIMIT 1";
        
        $st = $this->pdo->prepare($sql);
        $st->execute($categorias); // Pasa el array de categorías directamente
        $row = $st->fetch(PDO::FETCH_ASSOC);
        
        return $row ?: null;
    }
}
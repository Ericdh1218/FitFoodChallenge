<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class Glosario
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::conn();
    }

    /** Obtiene todos los términos ordenados */
    public function all(): array
    {
        $sql = "SELECT id, termino, definicion, categoria
                FROM glosario
                ORDER BY termino ASC";
        $st = $this->pdo->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Genera un quiz con N preguntas a partir del glosario
     */
    public function getGlosarioQuizData(int $numQuestions = 10): array
    {
        // 1) Traer términos
        $st = $this->pdo->query("SELECT id, termino, definicion FROM glosario");
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        // Reindexar por id => row
        $todos = [];
        foreach ($rows as $r) {
            $todos[(int)$r['id']] = $r;
        }

        // Se requieren al menos 4 términos (1 correcta + 3 distractores)
        if (count($todos) < 4) {
            return [];
        }

        // 2) Elegir IDs para preguntas
        $idsDisponibles = array_keys($todos);
        $nPreg = min($numQuestions, count($idsDisponibles));
        // array_rand puede devolver int si n=1, forzamos array
        $idsPregunta = (array) array_rand(array_flip($idsDisponibles), $nPreg);

        $quiz = [];

        foreach ($idsPregunta as $idCorrecto) {
            $idCorrecto = (int)$idCorrecto;
            $preg = $todos[$idCorrecto];

            // 3) Armar opciones: correcta + 3 distractores
            $idsDistractoresPool = $idsDisponibles;
            // quitar la correcta
            $idx = array_search($idCorrecto, $idsDistractoresPool, true);
            if ($idx !== false) unset($idsDistractoresPool[$idx]);

            // si no hay 3, recorta a lo que haya
            $nDistr = min(3, count($idsDistractoresPool));
            $idsDistractores = (array) array_rand(array_flip($idsDistractoresPool), $nDistr);

            // Construir [id => definicion]
            $opciones = [
                $idCorrecto => $preg['definicion']
            ];
            foreach ($idsDistractores as $idD) {
                $idD = (int)$idD;
                $opciones[$idD] = $todos[$idD]['definicion'];
            }

            // 4) Mezclar manteniendo claves
            $keys = array_keys($opciones);
            shuffle($keys);
            $opcionesMezcladas = [];
            foreach ($keys as $k) {
                $opcionesMezcladas[$k] = $opciones[$k];
            }

            // 5) Agregar pregunta
            $quiz[] = [
                'id_pregunta'   => $idCorrecto,
                'pregunta_texto'=> "¿Cuál es la definición de '{$preg['termino']}'?",
                'opciones'      => $opcionesMezcladas,   // [id => definicion]
                'id_correcto'   => $idCorrecto
            ];
        }

        return $quiz;
    }
    public function getByCategory(string $categoria): array
{
    $sql = "SELECT id, termino, definicion, categoria 
            FROM glosario 
            WHERE categoria = :cat
            ORDER BY termino ASC";
    $st = $this->pdo->prepare($sql);
    $st->execute(['cat' => $categoria]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}
} 
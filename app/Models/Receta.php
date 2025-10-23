<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class Receta
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DB::conn();
    }

    /**
     * Obtiene todas las recetas, con filtros.
     * Filtros: q (texto), categoria
     */
    public function all(array $f = []): array
    {
        $sql = "SELECT id, titulo, descripcion, imagen, categoria FROM recetas WHERE 1=1";
        $p = [];

        // Filtro de texto (q)
        if (!empty($f['q'])) {
            $sql .= " AND (titulo LIKE :q1 OR descripcion LIKE :q2)";
            $p['q1'] = '%' . $f['q'] . '%';
            $p['q2'] = '%' . $f['q'] . '%';
        }

        // Filtro de categoría
        if (!empty($f['categoria'])) {
            $sql .= " AND categoria = :categoria";
            $p['categoria'] = $f['categoria'];
        }
        
        // === NUEVOS FILTROS BOOLEANOS ===
        if (!empty($f['es_barato'])) {
            // Solo queremos las que tengan '1' (true)
            $sql .= " AND es_barato = 1"; 
            // No necesita parámetro porque el valor es fijo (1)
        }
        if (!empty($f['es_rapido'])) {
            $sql .= " AND es_rapido = 1";
        }
        if (!empty($f['es_snack_estudio'])) {
            $sql .= " AND es_snack_estudio = 1";
        }
        // ================================

        $sql .= " ORDER BY titulo ASC";
        
        $st = $this->pdo->prepare($sql);
        $st->execute($p);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca una receta por su ID
     */
    public function find(int $id): ?array
    {
        // Traemos todos los campos para la vista de detalle
        $st = $this->pdo->prepare("SELECT * FROM recetas WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Obtiene todas las categorías únicas de la BD
     */
    public function getCategorias(): array
    {
        $st = $this->pdo->query("SELECT DISTINCT categoria FROM recetas WHERE categoria IS NOT NULL ORDER BY categoria");
        return $st->fetchAll(PDO::FETCH_COLUMN);
    }
    // ... (tus funciones all(), find(), getCategorias() ... )

    /**
     * ==========================================================
     * AÑADE ESTA NUEVA FUNCIÓN
     * ==========================================================
     * Obtiene un número de ejemplos de recetas para categorías específicas.
     */
    public function getEjemplosPorCategorias(array $categorias, int $limite = 3): array
    {
        $resultados = [];
        
        // Preparamos la consulta una sola vez
        // Usamos RAND() para obtener variedad. LIMIT se pasará como parámetro.
        $sql = "SELECT id, titulo, imagen 
                FROM recetas 
                WHERE categoria = :categoria 
                ORDER BY RAND() 
                LIMIT :limite";
        $st = $this->pdo->prepare($sql);
        
        // Vinculamos el límite (debe ser un INT)
        $st->bindParam(':limite', $limite, PDO::PARAM_INT);

        foreach ($categorias as $cat) {
            // Vinculamos la categoría y ejecutamos
            $st->bindParam(':categoria', $cat);
            $st->execute();
            $resultados[$cat] = $st->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $resultados;
    }

}
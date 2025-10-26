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
        $sql = "SELECT id, titulo, descripcion, imagen, categoria
                FROM recetas
                WHERE user_id IS NULL";
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
    $st = $this->pdo->prepare(
        "SELECT id, user_id, titulo, descripcion, ingredientes, imagen, categoria, instrucciones,
                -- Seleccionar nuevas columnas manuales
                kcal_manual, proteinas_g_manual, grasas_g_manual, carbos_g_manual, fibra_g_manual
         FROM recetas
         WHERE id = :id"
    );
    $st->execute([':id' => $id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

    /**
     * Obtiene todas las categorías únicas de la BD
     */
   public function getCategorias(): array
{
    // Esta consulta busca los valores únicos de la columna 'categoria'
    $st = $this->pdo->query("SELECT DISTINCT categoria FROM recetas WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria");
    // fetchAll(PDO::FETCH_COLUMN) devuelve un array simple ['cat1', 'cat2', ...]
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

    // En app/Models/Receta.php

    // ... (all(), find(), getCategorias(), getEjemplos... existentes) ...

    /**
     * ==========================================
     * NUEVO MÉTODO: Crea una nueva receta en la tabla 'recetas'
     * ==========================================
     * @param array $data Datos de la receta (titulo, descripcion, etc.)
     * @return int|false El ID de la nueva receta o false si falla.
     */
    public function createRecipe(array $data)
    {
        $sql = "INSERT INTO recetas (user_id, titulo, descripcion, categoria, instrucciones, imagen)
            VALUES (:user_id, :titulo, :descripcion, :categoria, :instrucciones, :imagen)";
    $st = $this->pdo->prepare($sql);

    $exito = $st->execute([
        ':user_id' => $data['user_id'], // <-- Añadir parámetro
        ':titulo' => $data['titulo'],
        ':descripcion' => $data['descripcion'] ?? null,
        ':categoria' => $data['categoria'] ?? null,
        ':instrucciones' => $data['instrucciones'],
        ':imagen' => $data['imagen'] ?? null
    ]);

        if ($exito) {
            return (int)$this->pdo->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Añade ingredientes a una receta en 'receta_ingredientes'
     * ==========================================
     * @param int $recetaId ID de la receta
     * @param array $ingredientes Array asociativo [fdcId => ['name' => ..., 'amount' => ...]]
     * @return bool True si todos se insertaron, false si hubo algún error.
     */
    public function addIngredientsToRecipe(int $recetaId, array $ingredientes): bool
    {
        // Preparamos la consulta una sola vez
        $sql = "INSERT INTO receta_ingredientes (receta_id, fdc_id, cantidad_g) 
                VALUES (:receta_id, :fdc_id, :cantidad)";
        $st = $this->pdo->prepare($sql);
        
        $todoOk = true; // Bandera para saber si todo salió bien

        // Iteramos sobre los ingredientes recibidos del JS
        foreach ($ingredientes as $fdcId => $details) {
            $cantidad = (float)($details['amount'] ?? 0);
            if ($cantidad <= 0) { // Ignorar si la cantidad es inválida
                continue; 
            }
            
            // Ejecutamos la inserción para cada ingrediente
            $exito = $st->execute([
                ':receta_id' => $recetaId,
                ':fdc_id'    => (int)$fdcId, // Aseguramos que sea entero
                ':cantidad'  => $cantidad
            ]);
            
            // Si alguna inserción falla, marcamos la bandera
            if (!$exito) {
                $todoOk = false;
                error_log("Error al insertar ingrediente FDC ID: $fdcId para Receta ID: $recetaId");
                // Podríamos decidir parar aquí o continuar con los demás
            }
        }
        
        return $todoOk; // Devuelve true solo si TODOS se insertaron bien
    }
    // En app/Models/Receta.php (añadir este método)

/**
 * Obtiene todas las recetas creadas por un usuario específico.
 */
public function findByUserId(int $userId): array
    {
        // === AÑADE user_id AL SELECT ===
        $sql = "SELECT id, user_id, titulo, descripcion, imagen, categoria
                FROM recetas
                WHERE user_id = :user_id
                ORDER BY titulo ASC";
        // ==============================
        $st = $this->pdo->prepare($sql);
        $st->execute([':user_id' => $userId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca múltiples recetas por sus IDs.
     */
    public function findByIds(array $ids): array
    {
        if (empty($ids)) return [];
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        // Selecciona solo los campos necesarios para las tarjetas de sugerencia
        $sql = "SELECT id, titulo, imagen, categoria 
                FROM recetas 
                WHERE id IN ({$placeholders})";
        
        $st = $this->pdo->prepare($sql);
        $st->execute($ids);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * ==========================================
     * NUEVO MÉTODO: Obtiene N recetas que coincidan con un tag booleano
     * ==========================================
     */
    public function findByTag(string $tagName, int $limit = 3): array
    {
        // Lista blanca de tags permitidos para evitar inyección SQL
        $allowedTags = ['es_barato', 'es_rapido', 'es_snack_estudio'];
        if (!in_array($tagName, $allowedTags)) {
            return [];
        }

        // Usamos el nombre de la columna directamente (seguro gracias a la lista blanca)
        $sql = "SELECT id, titulo, imagen, categoria, descripcion 
                FROM recetas 
                WHERE {$tagName} = 1 AND user_id IS NULL -- Solo de las predefinidas
                ORDER BY RAND() 
                LIMIT :limite";
        
        $st = $this->pdo->prepare($sql);
        $st->bindParam(':limite', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    // ... (markAsRead(), hasRead(), getReadArticleIds() existentes) ...
/**
     * Obtiene los nutrientes cacheados para una lista de FDC IDs.
     * Devuelve un array asociativo [fdc_id => [nutriente => cantidad_por_100g, ...]]
     */
    public function getCachedNutrientsForFdcIds(array $fdcIds): array
    {
        if (empty($fdcIds)) {
            return [];
        }
        // Crea placeholders (?,?,?) para la consulta IN
        $placeholders = implode(',', array_fill(0, count($fdcIds), '?'));
        $sql = "SELECT fdc_id, nutrient_name, amount_per100, unit_name
                FROM alimento_nutrientes
                WHERE fdc_id IN ({$placeholders})";

        // Incluye solo nutrientes clave (puedes ajustar esta lista)
        $nutrientesClave = [
            'Energy', // Calorías (kcal)
            'Protein', // Proteína (g)
            'Total lipid (fat)', // Grasa Total (g)
            'Carbohydrate, by difference', // Carbohidratos (g)
            'Fiber, total dietary', // Fibra (g)
            'Sugars, total including NLEA', // Azúcares (g)
            // Puedes añadir Vitaminas (Vitamin C, Vitamin A), Minerales (Iron, Calcium, Sodium) si quieres
        ];
        $sql .= " AND nutrient_name IN (" . implode(',', array_fill(0, count($nutrientesClave), '?')) . ")";

        $st = $this->pdo->prepare($sql);
        // Combina los FDC IDs y los nombres de nutrientes para execute()
        $params = array_merge($fdcIds, $nutrientesClave);
        $st->execute($params);

        $results = [];
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            // Organiza por fdc_id y luego por nombre de nutriente
            $results[$row['fdc_id']][$row['nutrient_name']] = [
                'amount' => (float)$row['amount_per100'],
                'unit' => $row['unit_name']
            ];
        }
        return $results;
    }

    /**
     * Obtiene los ingredientes (con fdc_id y cantidad_g) de una receta específica.
     */
    public function getIngredientsForRecipe(int $recetaId): array
    {
        $sql = "SELECT ri.fdc_id, ri.cantidad_g, af.descripcion as nombre_alimento
                FROM receta_ingredientes ri
                JOIN alimentos_fdc af ON ri.fdc_id = af.fdc_id
                WHERE ri.receta_id = :receta_id";
        $st = $this->pdo->prepare($sql);
        $st->execute([':receta_id' => $recetaId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
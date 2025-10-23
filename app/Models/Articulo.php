<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class Articulo
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DB::conn();
    }

    /**
     * Obtiene todos los artículos (opcionalmente filtrados por categoría).
     */
    public function all(string $categoria = null): array
    {
        // === CAMBIO: Seleccionar url_externa y resumen ===
        $sql = "SELECT id, titulo, url_externa, resumen, imagen_url, categoria 
                FROM articulos";
        $params = [];

        if ($categoria) {
            $sql .= " WHERE categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        $sql .= " ORDER BY fecha_publicacion DESC";
        
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Busca un artículo por su ID.
     */
    // En app/Models/Articulo.php

// ... (all(), getCategorias(), etc.) ...

/**
 * Busca un artículo por su ID (incluyendo url_externa y resumen).
 */
public function find(int $id): ?array
{
    // === ASEGÚRATE QUE SELECCIONE LAS COLUMNAS NECESARIAS ===
    $st = $this->pdo->prepare(
        "SELECT id, titulo, url_externa, resumen, imagen_url, categoria, fecha_publicacion 
         FROM articulos 
         WHERE id = :id"
    );
    // ========================================================
    $st->execute([':id' => $id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

// ... (markAsRead(), hasRead(), getReadArticleIds() se quedan igual) ...

    /**
     * Obtiene todas las categorías únicas de artículos.
     */
    public function getCategorias(): array
    {
         $st = $this->pdo->query("SELECT DISTINCT categoria FROM articulos WHERE categoria IS NOT NULL ORDER BY categoria");
        return $st->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Marca un artículo como leído por un usuario
     * ==========================================
     * Usa INSERT IGNORE para evitar errores si ya existe la fila.
     */
    public function markAsRead(int $userId, int $articuloId): bool
    {
        // INSERT IGNORE no inserta si la clave primaria (user_id, articulo_id) ya existe
        $sql = "INSERT IGNORE INTO articulos_leidos (user_id, articulo_id) VALUES (:user_id, :articulo_id)";
        $st = $this->pdo->prepare($sql);
        
        return $st->execute([
            ':user_id' => $userId,
            ':articulo_id' => $articuloId
        ]);
        // Podríamos devolver $st->rowCount() > 0 si queremos saber si realmente se insertó
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Verifica si un usuario ya ha leído un artículo
     * ==========================================
     */
    public function hasRead(int $userId, int $articuloId): bool
    {
        $sql = "SELECT COUNT(*) FROM articulos_leidos WHERE user_id = :user_id AND articulo_id = :articulo_id";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':user_id' => $userId,
            ':articulo_id' => $articuloId
        ]);
        return $st->fetchColumn() > 0;
    }

    // En app/Models/Articulo.php

    // ... (después de hasRead) ...

    /**
     * ==========================================
     * NUEVO MÉTODO: Obtiene un array con los IDs de los artículos leídos por un usuario
     * ==========================================
     */
    public function getReadArticleIds(int $userId): array
    {
        $sql = "SELECT articulo_id FROM articulos_leidos WHERE user_id = :user_id";
        $st = $this->pdo->prepare($sql);
        $st->execute([':user_id' => $userId]);
        // fetchAll(PDO::FETCH_COLUMN) devuelve un array simple [id1, id2, ...]
        return $st->fetchAll(PDO::FETCH_COLUMN); 
    }
    
    // Aquí añadiremos la función markAsRead($userId, $articuloId) para gamificación después
}
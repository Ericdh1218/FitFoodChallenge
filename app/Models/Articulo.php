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
        $sql = "SELECT id, titulo, SUBSTRING(contenido, 1, 150) as extracto, imagen_url, categoria 
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
    public function find(int $id): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM articulos WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Obtiene todas las categorías únicas de artículos.
     */
    public function getCategorias(): array
    {
         $st = $this->pdo->query("SELECT DISTINCT categoria FROM articulos WHERE categoria IS NOT NULL ORDER BY categoria");
        return $st->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Aquí añadiremos la función markAsRead($userId, $articuloId) para gamificación después
}
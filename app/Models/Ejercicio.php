<?php
namespace App\Models;

use PDO;
use App\Config\DB;
class Ejercicio
{
    /**
     * Filtros: q (texto), grupo_muscular, tipo_entrenamiento, equipamiento
     */
    /**
     * Filtros: q (texto), grupo_muscular, tipo_entrenamiento, equipamiento
     */
    /**
     * Filtros: q (texto), grupo_muscular, tipo_entrenamiento, equipamiento
     */
    /**
     * Filtros: q (texto), grupo_muscular, tipo_entrenamiento, equipamiento
     */
    public static function all(array $f = []): array
    {
        $pdo = DB::conn();

        $sql = "SELECT id, nombre, descripcion, media_url,
                       grupo_muscular, tipo_entrenamiento, equipamiento, video_url
                  FROM ejercicios
                  WHERE 1=1";
        
        $p = []; 

        // === CORRECCIÓN ESTÁ AQUÍ ===
        if (!empty($f['q'])) {
            // 1. Usamos dos placeholders diferentes: :q1 y :q2
            $sql .= " AND (nombre LIKE :q1 OR descripcion LIKE :q2)";
            
            // 2. Añadimos AMBOS al array de parámetros
            $p['q1'] = '%' . $f['q'] . '%';
            $p['q2'] = '%' . $f['q'] . '%';
        }
        
        if (!empty($f['grupo_muscular'])) {
            $sql .= " AND grupo_muscular = :gm";
            $p['gm'] = $f['grupo_muscular'];
        }

        if (!empty($f['tipo_entrenamiento'])) {
            $sql .= " AND tipo_entrenamiento = :te";
            $p['te'] = $f['tipo_entrenamiento'];
        }

        if (!empty($f['equipamiento'])) {
            $sql .= " AND equipamiento = :eq";
            $p['eq'] = $f['equipamiento'];
        }

        $sql .= " ORDER BY nombre ASC";

        $st = $pdo->prepare($sql);
        
        // Ahora, si todos los filtros están llenos, 
        // $sql tendrá 5 placeholders (:q1, :q2, :gm, :te, :eq)
        // y $p tendrá 5 claves ('q1', 'q2', 'gm', 'te', 'eq')
        // ¡Ahora sí coinciden!
        $st->execute($p); 
        
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $pdo = DB::conn();
        $st = $pdo->prepare(
            "SELECT id, nombre, descripcion, media_url,
                    grupo_muscular, tipo_entrenamiento, equipamiento, video_url
             FROM ejercicios WHERE id = :id"
        );
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function grupos(): array
    {
        $pdo = DB::conn();
        $rows = $pdo->query("SELECT DISTINCT grupo_muscular FROM ejercicios ORDER BY grupo_muscular")
                    ->fetchAll(PDO::FETCH_COLUMN);
        return array_values(array_filter($rows));
    }

    public static function tipos(): array
    {
        $pdo = DB::conn();
        $rows = $pdo->query("SELECT DISTINCT tipo_entrenamiento FROM ejercicios ORDER BY tipo_entrenamiento")
                    ->fetchAll(PDO::FETCH_COLUMN);
        return array_values(array_filter($rows));
    }

    public static function equipos(): array
    {
        $pdo = DB::conn();
        $rows = $pdo->query("SELECT DISTINCT equipamiento FROM ejercicios ORDER BY equipamiento")
                    ->fetchAll(PDO::FETCH_COLUMN);
        return array_values(array_filter($rows));
    }

}
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
    
    /**
     * ==========================================
     * NUEVO MÉTODO: Busca ejercicios por filtros específicos
     * (¡El que faltaba!)
     * ==========================================
     */
    public static function findByFilter(array $filters, int $limit = 4): array
    {
        $pdo = DB::conn(); // Llama a la conexión

        $sql = "SELECT id, nombre, descripcion, media_url, grupo_muscular, equipamiento
                FROM ejercicios
                WHERE 1=1";
        $params = [];

        if (!empty($filters['equipamiento'])) {
            $sql .= " AND equipamiento = :equipamiento";
            $params[':equipamiento'] = $filters['equipamiento'];
        }
        if (!empty($filters['tipo_entrenamiento'])) {
            $sql .= " AND tipo_entrenamiento = :tipo_entrenamiento";
            $params[':tipo_entrenamiento'] = $filters['tipo_entrenamiento'];
        }
        
        // Asumiendo que las recetas predefinidas no tienen user_id
        // (Ajusta si tienes 'user_id' en la tabla 'ejercicios')
        // $sql .= " AND user_id IS NULL"; 

        $sql .= " ORDER BY RAND() LIMIT :limit"; // Aleatorios
        $params[':limit'] = $limit;
        
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * ==========================================
     * NUEVO MÉTODO (AJAX): Obtiene ejercicios aleatorios
     * y devuelve solo el HTML de las tarjetas.
     * ==========================================
     */
    public function getRandomEjerciciosAjax()
    {
        // (Opcional: podrías añadir protección de sesión aquí)
        // if (!isset($_SESSION['usuario_id'])) { exit; }

        // Filtros (podemos leerlos de $_GET si queremos ser flexibles)
        $filters = [
            'equipamiento' => $_GET['equipamiento'] ?? 'Sin Equipo',
        ];
        $limit = (int)($_GET['limit'] ?? 4);

        $ejercicioModel = new Ejercicio();
        $ejercicios = $ejercicioModel->findByFilter($filters, $limit);

        // --- Captura de Salida ---
        // Inicia un buffer para "capturar" el HTML que genera la vista parcial
        ob_start();
        
        // Carga una vista "parcial" (un archivo solo con el bucle foreach)
        // Pasamos las variables que la vista parcial necesitará
        view('home/_partial_ejercicio_cards', [
            'ejercicios' => $ejercicios
        ]);
        
        // Obtiene el HTML capturado y limpia el buffer
        $html = ob_get_clean();
        // -------------------------

        // Devuelve el HTML
        echo $html;
        exit;
    }

    public static function create(array $data): bool
    {
        $pdo = DB::conn();
        $sql = "INSERT INTO ejercicios (nombre, descripcion, media_url, video_url, grupo_muscular, tipo_entrenamiento, equipamiento) 
                VALUES (:nombre, :descripcion, :media_url, :video_url, :grupo_muscular, :tipo_entrenamiento, :equipamiento)";
        $st = $pdo->prepare($sql);
        
        return $st->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':media_url' => $data['media_url'],
            ':video_url' => $data['video_url'],
            ':grupo_muscular' => $data['grupo_muscular'],
            ':tipo_entrenamiento' => $data['tipo_entrenamiento'],
            ':equipamiento' => $data['equipamiento']
        ]);
        // (Nota: Asume que user_id es NULL por defecto para predefinidos)
    }

    /**
     * ==========================================
     * NUEVO MÉTODO (Admin): Actualiza un ejercicio
     * ==========================================
     */
    public static function update(int $id, array $data): bool
    {
        $pdo = DB::conn();
        $sql = "UPDATE ejercicios 
                SET 
                    nombre = :nombre,
                    descripcion = :descripcion,
                    media_url = :media_url,
                    video_url = :video_url,
                    grupo_muscular = :grupo_muscular,
                    tipo_entrenamiento = :tipo_entrenamiento,
                    equipamiento = :equipamiento
                WHERE id = :id";
        
        $st = $pdo->prepare($sql);
        return $st->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':media_url' => $data['media_url'],
            ':video_url' => $data['video_url'],
            ':grupo_muscular' => $data['grupo_muscular'],
            ':tipo_entrenamiento' => $data['tipo_entrenamiento'],
            ':equipamiento' => $data['equipamiento'],
            ':id' => $id
        ]);
    }

    /**
     * ==========================================
     * NUEVO MÉTODO (Admin): Elimina un ejercicio
     * ==========================================
     */
    public static function delete(int $id): bool
    {
        $pdo = DB::conn();
        // Borrar de rutina_ejercicios (personales)
        $st1 = $pdo->prepare("DELETE FROM rutina_ejercicios WHERE ejercicio_id = :id");
        $st1->execute([':id' => $id]);
        
        // Borrar de rutina_prediseñada_ejercicios
        $st2 = $pdo->prepare("DELETE FROM rutina_prediseñada_ejercicios WHERE ejercicio_id = :id");
        $st2->execute([':id' => $id]);

        // Borrar el ejercicio principal
        $st3 = $pdo->prepare("DELETE FROM ejercicios WHERE id = :id");
        return $st3->execute([':id' => $id]);
    }
}
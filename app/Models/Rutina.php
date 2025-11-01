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
    public function createPersonal(int $userId, string $nombreRutina)
    {
        $sql = "INSERT INTO rutinas (user_id, nombre_rutina) VALUES (:user_id, :nombre)";
        $st = $this->pdo->prepare($sql);
        
        $exito = $st->execute([
            ':user_id' => $userId,
            ':nombre'  => $nombreRutina
        ]);

        if ($exito) {
            return (int)$this->pdo->lastInsertId(); // Devuelve el ID recién creado
        } else {
            return false;
        }
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Busca una rutina personal por ID y User ID
     * ==========================================
     */
    public function findPersonal(int $rutinaId, int $userId): ?array
    {
        $sql = "SELECT * FROM rutinas WHERE id = :id AND user_id = :user_id";
        $st = $this->pdo->prepare($sql);
        $st->execute([':id' => $rutinaId, ':user_id' => $userId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Obtiene los ejercicios de una rutina personal
     * ==========================================
     */
    public function getEjerciciosDeRutinaPersonal(int $rutinaId): array
    {
        // === ACTUALIZAR SELECT ===
        $sql = "SELECT 
                    e.id, e.nombre, e.grupo_muscular, e.equipamiento, e.media_url, 
                    re.id as rutina_ejercicio_id, -- ID de la fila en rutina_ejercicios (para borrar)
                    re.series,                   -- Nuevo
                    re.repeticiones              -- Nuevo
                FROM ejercicios e
                JOIN rutina_ejercicios re ON e.id = re.ejercicio_id
                WHERE re.rutina_id = :rutina_id
                ORDER BY re.id ASC -- Ordenar por orden de inserción (o podríamos añadir un campo 'orden')
                "; 
        
        $st = $this->pdo->prepare($sql);
        $st->execute([':rutina_id' => $rutinaId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Añade un ejercicio a una rutina personal
     * ==========================================
     * (Añade validación para evitar duplicados)
     */
    public function addEjercicioARutina(int $rutinaId, int $ejercicioId, $series, $repeticiones): bool 
    {
        // 1. Verificar si ya existe (misma lógica)
        $checkSql = "SELECT COUNT(*) FROM rutina_ejercicios WHERE rutina_id = :rutina_id AND ejercicio_id = :ejercicio_id";
        $checkSt = $this->pdo->prepare($checkSql);
        $checkSt->execute([':rutina_id' => $rutinaId, ':ejercicio_id' => $ejercicioId]);
        if ($checkSt->fetchColumn() > 0) {
             // Si ya existe, podríamos ACTUALIZAR series/reps en lugar de fallar,
             // pero por ahora lo dejamos como "éxito sin hacer nada".
            return true; 
        }

        // 2. Insertar la relación CON series y repeticiones
        // === ACTUALIZAR SQL y PARÁMETROS ===
        $sql = "INSERT INTO rutina_ejercicios (rutina_id, ejercicio_id, series, repeticiones) 
                VALUES (:rutina_id, :ejercicio_id, :series, :repeticiones)";
        $st = $this->pdo->prepare($sql);
        
        return $st->execute([
            ':rutina_id'    => $rutinaId,
            ':ejercicio_id' => $ejercicioId,
            ':series'       => $series,        // Nuevo
            ':repeticiones' => $repeticiones   // Nuevo
        ]);
    }
    public function findByUserId(int $userId): array
    {
        $sql = "SELECT id, nombre_rutina 
                FROM rutinas 
                WHERE user_id = :user_id 
                ORDER BY nombre_rutina ASC";
        $st = $this->pdo->prepare($sql);
        $st->execute([':user_id' => $userId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * ==========================================
     * NUEVO MÉTODO: Elimina una rutina personal específica de un usuario
     * ==========================================
     */
    public function deletePersonal(int $rutinaId, int $userId): bool
    {
        // Doble seguridad: nos aseguramos de borrar solo si el ID y user_ID coinciden
        $sql = "DELETE FROM rutinas WHERE id = :id AND user_id = :user_id";
        $st = $this->pdo->prepare($sql);
        
        return $st->execute([
            ':id' => $rutinaId,
            ':user_id' => $userId
        ]);
        // Nota: Gracias a las restricciones FOREIGN KEY con ON DELETE CASCADE,
        // al borrar la rutina de la tabla 'rutinas', automáticamente se borrarán
        // las filas correspondientes en 'rutina_ejercicios'.
    }
    /**
     * ==========================================
     * NUEVO MÉTODO: Verifica si una fila de rutina_ejercicios pertenece a una rutina del usuario
     * ==========================================
     */
    public function verificarPertenenciaEjercicioRutina(int $rutinaEjercicioId, int $userId): bool
    {
        // Consultamos si existe una rutina_ejercicio con ese ID 
        // y si la rutina asociada (r.id) pertenece al usuario (r.user_id)
        $sql = "SELECT COUNT(*) 
                FROM rutina_ejercicios re
                JOIN rutinas r ON re.rutina_id = r.id
                WHERE re.id = :rutina_ejercicio_id AND r.user_id = :user_id";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':rutina_ejercicio_id' => $rutinaEjercicioId, 
            ':user_id' => $userId
        ]);
        return $st->fetchColumn() > 0;
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Elimina una relación específica de rutina_ejercicios por su ID
     * ==========================================
     */
    public function removeEjercicioFromRutina(int $rutinaEjercicioId): bool
    {
        $sql = "DELETE FROM rutina_ejercicios WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        return $st->execute([':id' => $rutinaEjercicioId]);
    }
    /**
     * ==========================================
     * NUEVO MÉTODO (Admin): Obtiene TODAS las rutinas (predefinidas y de usuarios)
     * ==========================================
     */
    public function getAllAdmin(): array
    {
        $sql = "SELECT 
                    r.id, r.nombre_rutina, r.nivel, r.user_id, 
                    u.nombre as autor_nombre
                FROM rutinas r
                LEFT JOIN users u ON r.user_id = u.id
                ORDER BY r.id DESC";
        $st = $this->pdo->prepare($sql);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ==========================================
     * NUEVO MÉTODO (Admin): Crea una nueva rutina (predefinida)
     * ==========================================
     */
    public function createPredefinida(array $data): bool
    {
        $sql = "INSERT INTO rutinas (nombre_rutina, descripcion, nivel, tipo_rutina, user_id) 
                VALUES (:nombre, :descripcion, :nivel, :tipo, NULL)"; // user_id es NULL
        $st = $this->pdo->prepare($sql);
        
        return $st->execute([
            ':nombre' => $data['nombre_rutina'],
            ':descripcion' => $data['descripcion'],
            ':nivel' => $data['nivel'],
            ':tipo' => $data['tipo_rutina']
        ]);
    }

    /**
     * ==========================================
     * NUEVO MÉTODO (Admin): Actualiza una rutina
     * ==========================================
     */
    public function updateRutina(int $id, array $data): bool
    {
        $sql = "UPDATE rutinas 
                SET 
                    nombre_rutina = :nombre,
                    descripcion = :descripcion,
                    nivel = :nivel,
                    tipo_rutina = :tipo
                WHERE id = :id";
        
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            ':nombre' => $data['nombre_rutina'],
            ':descripcion' => $data['descripcion'],
            ':nivel' => $data['nivel'],
            ':tipo' => $data['tipo_rutina'],
            ':id' => $id
        ]);
    }

    /**
     * ==========================================
     * NUEVO MÉTODO (Admin): Elimina una rutina (y sus ejercicios asociados)
     * ==========================================
     */
    public function deleteRutina(int $id): bool
    {
        // Borrar de rutinas personales (si algún usuario la creó, aunque no debería)
        $st1 = $this->pdo->prepare("DELETE FROM rutina_ejercicios WHERE rutina_id = :id");
        $st1->execute([':id' => $id]);
        
        // Borrar de rutinas predefinidas
        $st2 = $this->pdo->prepare("DELETE FROM rutina_prediseñada_ejercicios WHERE rutina_id = :id");
        $st2->execute([':id' => $id]);

        // Borrar la rutina principal
        $st3 = $this->pdo->prepare("DELETE FROM rutinas WHERE id = :id");
        return $st3->execute([':id' => $id]);
    }

    // En app/Models/Rutina.php

/** Añade un ejercicio a una rutina predefinida */
public function addEjercicioPredefinido(int $rutinaId, int $ejercicioId, string $seriesReps): bool
{
    $sql = "INSERT INTO rutina_prediseñada_ejercicios (rutina_id, ejercicio_id, series_reps) 
            VALUES (:rid, :eid, :series)";
    $st = $this->pdo->prepare($sql);
    return $st->execute([':rid' => $rutinaId, ':eid' => $ejercicioId, ':series' => $seriesReps]);
}

/** Quita un ejercicio de una rutina predefinida */
public function removeEjercicioPredefinido(int $rutinaId, int $ejercicioId): bool
{
    $sql = "DELETE FROM rutina_prediseñada_ejercicios WHERE rutina_id = :rid AND ejercicio_id = :eid";
    $st = $this->pdo->prepare($sql);
    return $st->execute([':rid' => $rutinaId, ':eid' => $ejercicioId]);
}
}

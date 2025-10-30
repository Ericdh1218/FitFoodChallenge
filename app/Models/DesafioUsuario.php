<?php
namespace App\Models;
use App\Config\DB;
use PDO;

class DesafioUsuario
{
    protected $pdo;
    public function __construct() { $this->pdo = DB::conn(); }

    /** Busca el progreso de un usuario en un desafío específico */
    public function findProgreso(int $userId, int $desafioId): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM desafios_usuarios WHERE user_id = :uid AND desafio_id = :did");
        $st->execute([':uid' => $userId, ':did' => $desafioId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Inscribe a un usuario en un desafío si no está ya inscrito */
    public function unirse(int $userId, int $desafioId): bool
    {
        // INSERT IGNORE previene el error si ya existe (clave unica 'usuario_desafio_unico')
        $sql = "INSERT IGNORE INTO desafios_usuarios (user_id, desafio_id, progreso_actual) 
                VALUES (:uid, :did, 0)";
        $st = $this->pdo->prepare($sql);
        return $st->execute([':uid' => $userId, ':did' => $desafioId]);
    }

    // (Aquí irán funciones como 'actualizarProgreso', 'completarDesafio', etc.)
    /**
     * ==========================================
     * NUEVO MÉTODO: Actualiza el progreso de un desafío basado en el tipo de hábito
     * ==========================================
     */
    public function actualizarProgreso(int $userId, string $tipoHabito): bool
    {
        // Esta consulta compleja hace todo en un solo paso:
        // 1. Encuentra el desafío (d) que enlaza con el tipo de hábito (ej. 'agua')
        // 2. Se une a la inscripción del usuario (du) para ese desafío
        // 3. Incrementa 'progreso_actual' en 1
        // 4. Marca 'completado' = 1 SI el nuevo progreso es >= a la duración
        // 5. SOLO lo hace si el desafío NO está ya completado
        
        $sql = "UPDATE `desafios_usuarios` du
                JOIN `desafios` d ON du.desafio_id = d.id
                SET 
                    du.progreso_actual = du.progreso_actual + 1,
                    du.completado = IF(du.progreso_actual + 1 >= d.duracion_dias, 1, 0),
                    du.fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE 
                    du.user_id = :user_id 
                    AND d.tipo_habito_link = :tipo_habito
                    AND du.completado = 0"; // <-- Solo actualiza retos no completados

        $st = $this->pdo->prepare($sql);
        
        return $st->execute([
            ':user_id' => $userId,
            ':tipo_habito' => $tipoHabito
        ]);
    }
        /**
     * Busca el progreso de un usuario en un desafío basado en el tipo_habito_link.
     */
    public function findProgresoByHabito(int $userId, string $tipoHabito): ?array
    {
        // Busca el progreso (du) uniéndolo con el desafío (d) que tenga el tipo_habito_link
        $sql = "SELECT du.*, d.duracion_dias
                FROM desafios_usuarios du
                JOIN desafios d ON du.desafio_id = d.id
                WHERE du.user_id = :user_id AND d.tipo_habito_link = :tipo_habito AND du.completado = 0
                LIMIT 1"; // Asume que un usuario solo puede tener un reto activo de ese tipo
        
        $st = $this->pdo->prepare($sql);
        $st->execute([':user_id' => $userId, ':tipo_habito' => $tipoHabito]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
        // (En el futuro, aquí podrías verificar $st->rowCount() > 0 y si se completó,
        // llamar a un servicio que otorgue la recompensa_xp)
    }
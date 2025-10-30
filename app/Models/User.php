<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class User
{
    /**
     * @var ?PDO La conexión a la base de datos
     */
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DB::conn();
    }

    /**
     * Busca un usuario por su correo electrónico.
     */
    public function findByEmail(string $correo): ?array
    {
        // Esta consulta está bien (usa la tabla 'users')
        $st = $this->pdo->prepare("SELECT * FROM users WHERE correo = :correo");
        $st->execute([':correo' => $correo]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        
        return $row ?: null;
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     */
    public function create(array $data): bool
    {
        // ==========================================================
        // ===               SOLUCIÓN A LOS ERRORES               ===
        // ==========================================================
        
        // 1. Usamos los nombres de columna REALES de tu base de datos:
        //    'password_hash' y 'edad' (con e minúscula).
        $sql = "INSERT INTO users (nombre, correo, password_hash, edad, genero) 
                VALUES (:nombre, :correo, :password_hash, :edad, :genero)";
        
        $st = $this->pdo->prepare($sql);
        
        // 2. Mapeamos los datos del CONTROLADOR a los placeholders del SQL:
        return $st->execute([
            ':nombre'        => $data['nombre'],
            ':correo'        => $data['correo'],
            // El controlador envía 'contrasena', pero el SQL espera ':password_hash'
            ':password_hash' => $data['contrasena'], 
            // El controlador envía 'edad', pero el SQL espera ':edad'
            ':edad'          => $data['edad'],
            ':genero'       => $data['genero']
        ]);
    }

    // ... en app/Models/User.php
public function updateProfile(int $id, array $data): bool
{
    $sql = "UPDATE users SET 
                nivel_actividad = :nivel_actividad,
                objetivo_principal = :objetivo_principal,
                nivel_alimentacion = :nivel_alimentacion,
                horas_sueno = :horas_sueno,
                consumo_agua = :consumo_agua,
                -- === NUEVAS LÍNEAS ===
                peso = :peso,
                altura = :altura,
                imc = :imc,
                genero = :genero
            WHERE id = :id";
    
    $st = $this->pdo->prepare($sql);
    
    return $st->execute([
        ':nivel_actividad'    => $data['nivel_actividad'],
        ':objetivo_principal' => $data['objetivo_principal'],
        ':nivel_alimentacion' => $data['nivel_alimentacion'],
        ':horas_sueno'        => $data['horas_sueno'],
        ':consumo_agua'       => $data['consumo_agua'],
        // === NUEVAS LÍNEAS ===
        ':peso'               => $data['peso'],
        ':altura'             => $data['altura'],
        ':imc'                => $data['imc'],
        ':genero'             => $data['genero'],
        ':id'                 => $id
    ]);
}
    public function findById(int $id): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
    // ... en app/Models/User.php
// ... (después de updateProfile) ...

/**
 * Actualiza solo el peso y el IMC de un usuario.
 */
public function updateBiometrics(int $id, float $peso, float $imc): bool
{
    $sql = "UPDATE users SET peso = :peso, imc = :imc WHERE id = :id";
    $st = $this->pdo->prepare($sql);
    
    return $st->execute([
        ':peso' => $peso,
        ':imc'  => $imc,
        ':id'   => $id
    ]);
}
    public function addXp(int $userId, int $xpAmount): array
    {
        // 1. Añadir el XP
        $sqlAddXp = "UPDATE users SET xp = xp + :xp WHERE id = :id";
        $st = $this->pdo->prepare($sqlAddXp);
        $st->execute([':xp' => $xpAmount, ':id' => $userId]);

        // 2. Obtener el estado actual del usuario (nuevo XP y nivel actual)
        $stUser = $this->pdo->prepare("SELECT xp, level FROM users WHERE id = :id");
        $stUser->execute([':id' => $userId]);
        $usuario = $stUser->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            return ['success' => false];
        }

        $xpActual = $usuario['xp'];
        $nivelActual = $usuario['level'];

        // 3. Buscar el nivel MÁS ALTO que el usuario debería tener con su XP actual
        $stNivel = $this->pdo->prepare(
            "SELECT level, nombre_nivel FROM niveles 
             WHERE xp_requerido <= :xp 
             ORDER BY level DESC 
             LIMIT 1"
        );
        $stNivel->execute([':xp' => $xpActual]);
        $nuevoNivelData = $stNivel->fetch(PDO::FETCH_ASSOC);
        
        $nuevoNivelNum = $nuevoNivelData ? $nuevoNivelData['level'] : 1;

        $resultado = [
            'success' => true,
            'xp_ganado' => $xpAmount,
            'xp_total' => $xpActual,
            'subio_de_nivel' => false,
            'nombre_nuevo_nivel' => ''
        ];

        // 4. Si el nivel calculado es MÁS ALTO que su nivel actual, subirlo
        if ($nuevoNivelNum > $nivelActual) {
            $stUpdateLvl = $this->pdo->prepare("UPDATE users SET level = :level WHERE id = :id");
            $stUpdateLvl->execute([':level' => $nuevoNivelNum, ':id' => $userId]);
            
            $resultado['subio_de_nivel'] = true;
            $resultado['nombre_nuevo_nivel'] = $nuevoNivelData['nombre_nivel'];
        }
        
        return $resultado;
    }

    public function getLevelInfo(int $level): ?array
    {
        // Busca el XP requerido y el nombre para el nivel dado
        $st = $this->pdo->prepare("SELECT xp_requerido, nombre_nivel FROM niveles WHERE level = :level");
        $st->execute([':level' => $level]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Obtiene el XP requerido para el siguiente nivel
     * ==========================================
     */
    public function getNextLevelXp(int $currentLevel): ?int
    {
        $nextLevel = $currentLevel + 1;
        $st = $this->pdo->prepare("SELECT xp_requerido FROM niveles WHERE level = :level");
        $st->execute([':level' => $nextLevel]);
        $xp = $st->fetchColumn(); // Devuelve solo el valor de xp_requerido
        return $xp ? (int)$xp : null; // Devuelve el XP o null si es el nivel máximo
    }
    /**
     * ==========================================
     * NUEVO MÉTODO: Obtiene los N usuarios con más XP
     * ==========================================
     */
    public function getLeaderboard(int $limit = 10): array
{
    $sql = "
        SELECT id, nombre, xp, level
        FROM users
        WHERE tipo_user = 0
        ORDER BY xp DESC, level DESC, nombre ASC
        LIMIT :lim
    ";
    $st = $this->pdo->prepare($sql);
    $st->bindValue(':lim', $limit, \PDO::PARAM_INT);
    $st->execute();
    return $st->fetchAll(\PDO::FETCH_ASSOC);
}

    /**
     * ==========================================
     * NUEVO MÉTODO: Obtiene el ranking (posición) de un usuario
     * ==========================================
     */
public function getUserRank(int $userId): ?array
{
    $sql = "
        SELECT r.posicion AS `rank`, r.xp, r.level
        FROM (
            SELECT 
                id, xp, level,
                ROW_NUMBER() OVER (ORDER BY xp DESC, level DESC, nombre ASC) AS posicion
            FROM users
        ) AS r
        WHERE r.id = :user_id
    ";

    $st = $this->pdo->prepare($sql);
    $st->execute([':user_id' => $userId]);
    $row = $st->fetch(\PDO::FETCH_ASSOC);

    return $row ?: null;
}

}
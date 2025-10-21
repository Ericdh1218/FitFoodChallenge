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
        //    'password_hash' y 'Edad' (con E mayúscula).
        $sql = "INSERT INTO users (nombre, correo, password_hash, Edad) 
                VALUES (:nombre, :correo, :password_hash, :edad)";
        
        $st = $this->pdo->prepare($sql);
        
        // 2. Mapeamos los datos del CONTROLADOR a los placeholders del SQL:
        return $st->execute([
            ':nombre'        => $data['nombre'],
            ':correo'        => $data['correo'],
            // El controlador envía 'contrasena', pero el SQL espera ':password_hash'
            ':password_hash' => $data['contrasena'], 
            // El controlador envía 'edad', pero el SQL espera ':edad'
            ':edad'          => $data['edad']
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
                imc = :imc
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
}
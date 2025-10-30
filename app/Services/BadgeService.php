<?php
namespace App\Services;

use App\Config\DB;
use App\Models\User; // Para dar XP
use PDO;

class BadgeService
{
    protected $pdo;
    protected $userModel;

    public function __construct()
    {
        $this->pdo = DB::conn();
        $this->userModel = new User(); // Usaremos el modelo User para añadir XP
    }

    /**
     * Otorga una insignia a un usuario si no la tiene.
     * Devuelve el nombre de la insignia si se acaba de ganar, o null si ya la tenía/no existe.
     */
    public function otorgarInsigniaPorCodigo(int $userId, string $codigoInsignia): ?string
    {
        // 1. Buscar la insignia por su código
        $insignia = $this->_getInsigniaPorCodigo($codigoInsignia);
        if (!$insignia) {
            error_log("Intento de otorgar insignia no existente: $codigoInsignia");
            return null; // La insignia no existe
        }

        // 2. Verificar si el usuario ya tiene esta insignia
        if ($this->_checkSiYaLaTiene($userId, $insignia['id'])) {
            return null; // El usuario ya la tiene
        }

        // 3. ¡El usuario no la tiene! Otorgarla.
        $this->_insertarInsigniaUsuario($userId, $insignia['id']);
        
        // 4. Otorgar el XP extra por la insignia
        if ($insignia['xp_recompensa'] > 0) {
            $this->userModel->addXp($userId, $insignia['xp_recompensa']);
        }
        
        // 5. Devolver el nombre de la insignia para el mensaje flash
        return $insignia['nombre'];
    }

    /** Helper: Busca una insignia por su código */
    private function _getInsigniaPorCodigo(string $codigo): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM insignias WHERE codigo = :codigo");
        $st->execute([':codigo' => $codigo]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Helper: Verifica si el usuario ya tiene la insignia */
    private function _checkSiYaLaTiene(int $userId, int $insigniaId): bool
    {
        $st = $this->pdo->prepare("SELECT 1 FROM insignias_usuarios WHERE user_id = :uid AND insignia_id = :iid");
        $st->execute([':uid' => $userId, ':iid' => $insigniaId]);
        return $st->fetchColumn() !== false;
    }

    /** Helper: Inserta la insignia ganada en la tabla */
    private function _insertarInsigniaUsuario(int $userId, int $insigniaId): bool
    {
        $st = $this->pdo->prepare("INSERT INTO insignias_usuarios (user_id, insignia_id) VALUES (:uid, :iid)");
        return $st->execute([':uid' => $userId, ':iid' => $insigniaId]);
    }

    public function getAll(): array {
        $sql = "SELECT codigo, nombre, descripcion, icono_url, xp_recompensa FROM insignias ORDER BY id ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserBadgeCodes(int $userId): array {
    $sql = "SELECT i.codigo
            FROM insignias i
            JOIN insignias_usuarios iu ON iu.insignia_id = i.id
            WHERE iu.user_id = ?";
    $st = $this->pdo->prepare($sql);
    $st->execute([$userId]);
    return array_column($st->fetchAll(PDO::FETCH_ASSOC), 'codigo');
}

}
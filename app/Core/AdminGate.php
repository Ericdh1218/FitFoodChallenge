<?php
namespace App\Core;

class AdminGate
{
    // 👈 AJUSTA ESTO SEGÚN TU BD:
    // Si en tu BD: admin = 0  -> pon 0 aquí
    // Si en tu BD: admin = 1  -> pon 1 aquí
    private const ADMIN_VALUE = 1;

    public static function requireAdmin(): void
    {
        if (!isset($_SESSION['usuario_id'], $_SESSION['usuario_tipo'])) {
            header('Location: ' . url('/login')); exit;
        }
        if ((int)$_SESSION['usuario_tipo'] !== self::ADMIN_VALUE) {
            http_response_code(403);
            echo 'Acceso restringido (admin).'; exit;
        }
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION['usuario_tipo'])
            && (int)$_SESSION['usuario_tipo'] === self::ADMIN_VALUE;
    }
}

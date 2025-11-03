<?php
namespace App\Core;

class AdminMiddleware
{
    public static function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        // Soporte para claves de sesión diferentes (usuario_tipo o tipo_user)
        $tipo = $_SESSION['tipo_user'] ?? $_SESSION['usuario_tipo'] ?? null;

        // Si no es admin (tipo_user != 0)
        if ((int)$tipo !== 0) {
            header('Location: ' . url('/'));
            exit;
        }
    }
}

<?php
namespace App\Core;

use App\Models\User;

class AdminMiddleware
{
    /**
     * Verifica si el usuario es un administrador.
     * Si no lo es, lo redirige fuera del panel de admin.
     */
    public static function check()
    {
        // 1. ¿Hay sesión?
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        // 2. ¿El usuario es admin (tipo_user = 0)?
        $userModel = new User();
        $usuario = $userModel->findById($_SESSION['usuario_id']);

        if (!$usuario || $usuario['tipo_user'] != 0) {
            // No es admin, lo sacamos
            $_SESSION['flash_message'] = "No tienes permiso para acceder a esta área.";
            header('Location: ' . url('/')); // Redirige al inicio
            exit;
        }
        
        // Si todo está bien, no hace nada y el script continúa.
    }
}
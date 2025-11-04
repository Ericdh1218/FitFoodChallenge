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

        // Lee la clave que guardamos en login. 
        // (Soporta 'usuario_tipo' por compatibilidad, por si lo usaste en otro lado)
        $tipo = $_SESSION['tipo_user'] ?? $_SESSION['usuario_tipo'] ?? null;

        // ✅ admin = 1 en tu BD
        if ((int)$tipo !== 1) {
            header('Location: ' . url('/'));
            exit;
        }
    }
}

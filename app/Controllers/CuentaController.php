<?php
namespace App\Controllers;

use App\Models\User; // Asegúrate de importar User

class CuentaController
{
    public function index()
    {
        // === PASO DE SEGURIDAD ===
        // Si el usuario NO está logueado...
        if (!isset($_SESSION['usuario_id'])) {
            // ...lo mandamos al login.
            header('Location: ' . url('/login'));
            exit;
        }

        // Si está logueado, buscamos sus datos
        $userModel = new User();
        // Usamos un nuevo método 'findById' (que crearemos en el modelo)
        $usuario = $userModel->findById($_SESSION['usuario_id']);

        // Mostramos la vista con los datos del usuario
        view('home/miCuenta', ['usuario' => $usuario]);
    }
}
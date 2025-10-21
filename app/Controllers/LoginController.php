<?php
namespace App\Controllers;

use App\Models\User;

class LoginController
{
    /**
     * Muestra la página de login.
     * $error es un mensaje opcional si el login falla.
     */
    public function show($error = null)
    {
        // Pasa la variable de error a la vista
        view('home/login', ['error' => $error]);
    }

    /**
     * Procesa el formulario de login.
     */
    public function store()
    {
        $correo = $_POST['correo'] ?? null;
        $contrasena = $_POST['contrasena'] ?? null;

        // 1. Validación simple
        if (empty($correo) || empty($contrasena)) {
            return $this->show('Correo y contraseña son obligatorios.');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($correo);

        // 2. Verificar que el usuario exista
        if (!$user) {
            return $this->show('Credenciales incorrectas.');
        }

        // 3. Verificar la contraseña
        //    Compara la contraseña del formulario con el HASH de la BD
        if (password_verify($contrasena, $user['password_hash'])) {
            
            // ¡Login exitoso!
            session_regenerate_id(true); // Seguridad
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];

            // Redirigir al dashboard principal (o a /progreso)
            header('Location: ' . url('/'));
            exit;

        } else {
            // Contraseña incorrecta
            return $this->show('Credenciales incorrectas.');
        }
    }
    public function logout()
    {
        // Asegura que la sesión esté iniciada (aunque ya debería estarlo)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Desarma el array $_SESSION
        session_unset();

        // 2. Destruye la sesión en el servidor
        session_destroy();

        // 3. Redirige al inicio
        header('Location: ' . url('/'));
        exit;
    }
}
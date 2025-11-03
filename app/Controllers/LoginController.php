<?php
namespace App\Controllers;
use App\Core\AdminGate;
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
    $correo     = $_POST['correo'] ?? null;
    $contrasena = $_POST['contrasena'] ?? null;

    if (empty($correo) || empty($contrasena)) {
        return $this->show('Correo y contraseña son obligatorios.');
    }

    $userModel = new User();
    $user      = $userModel->findByEmail($correo);

    if (!$user) {
        return $this->show('Credenciales incorrectas.');
    }

    if (password_verify($contrasena, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id']     = (int)$user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        $_SESSION['tipo_user']      = (int)$user['tipo_user']; // 👈 guarda el rol

        // Redirección por rol
        if ((int)$_SESSION['tipo_user'] === 0) {
            header('Location: ' . url('/admin')); // admin = 0
        } else {
            header('Location: ' . url('/'));      // usuario normal
        }
        exit;
    }

    return $this->show('Credenciales incorrectas.');
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
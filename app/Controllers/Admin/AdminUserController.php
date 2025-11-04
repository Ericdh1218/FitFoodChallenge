<?php
namespace App\Controllers\Admin;

use App\Core\AdminMiddleware;
use App\Models\User;

class AdminUserController
{
    protected $userModel;

    public function __construct()
    {
        // 1. ¡Proteger!
        AdminMiddleware::check();
        $this->userModel = new User();
    }

    /**
     * Muestra la lista de todos los usuarios.
     * Responde a: GET /admin/users
     */
    public function index()
    {
        $usuarios = $this->userModel->getAllUsers();
        
        view('admin/users/index', [
            'title' => 'Gestionar Usuarios',
            'usuarios' => $usuarios
        ], 'admin'); // Usa el layout de admin
    }

    /**
     * Muestra el formulario para editar un usuario.
     * Responde a: GET /admin/users/edit?id=...
     */
    public function edit()
    {
        $userId = (int)($_GET['id'] ?? 0);
        if (!$userId) {
             header('Location: ' . url('/admin/users')); exit;
        }

        $usuario = $this->userModel->findById($userId);
        if (!$usuario) {
             header('Location: ' . url('/admin/users')); exit;
        }
        
        view('admin/users/edit', [
            'title' => 'Editar Usuario',
            'usuario' => $usuario
        ], 'admin');
    }

    /**
     * Procesa la actualización de un usuario.
     * Responde a: POST /admin/users/update
     */
    public function update()
    {
        $userId = (int)($_POST['user_id'] ?? 0);
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'tipo_user' => (int)($_POST['tipo_user'] ?? 1), // Default 1 (normal)
            'level' => (int)($_POST['level'] ?? 1),
            'xp' => (int)($_POST['xp'] ?? 0)
        ];

        // (Validación simple)
        if (!$userId || empty($data['nombre']) || empty($data['correo'])) {
            // Error
            $_SESSION['flash_message'] = 'Error: Faltan datos.';
            header('Location: ' . url('/admin/users/edit?id=' . $userId));
            exit;
        }
        
        $this->userModel->updateUserAsAdmin($userId, $data);
        
        $_SESSION['flash_message'] = '¡Usuario actualizado con éxito!';
        header('Location: ' . url('/admin/users'));
        exit;
    }

    /**
     * Procesa la eliminación de un usuario.
     * Responde a: POST /admin/users/delete
     */
    public function delete()
    {
        $userId = (int)($_POST['user_id'] ?? 0);
        
        if ($userId === (int)($_SESSION['usuario_id'])) {
             $_SESSION['flash_message'] = 'Error: No puedes eliminarte a ti mismo.';
             header('Location: ' . url('/admin/users'));
             exit;
        }

        $this->userModel->deleteUser($userId);
        
        $_SESSION['flash_message'] = '¡Usuario eliminado!';
        header('Location: ' . url('/admin/users'));
        exit;
    }
}
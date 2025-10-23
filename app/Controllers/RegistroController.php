<?php
namespace App\Controllers;

use App\Models\User;

class RegistroController
{
    /**
     * Muestra el formulario de registro
     */
    public function show()
    {
        // Usamos la función helper 'view'
        view('home/registro');
    }

    /**
     * Procesa el formulario de registro
     */
    public function store()
    {
        $user = new User();

        $data = [
            'nombre'     => $_POST['nombre'] ?? '',
            'correo'     => $_POST['correo'] ?? '',
            'contrasena' => $_POST['contrasena'] ?? '',
            'edad'       => $_POST['edad'] ?? '',
            'genero'     => $_POST['genero'] ?? null,
        ];

        // Validación básica
        if (empty($data['nombre']) || empty($data['correo']) || empty($data['contrasena']) || empty($data['edad'])) {
            // Es mejor redirigir con un error
            // Por ahora, lo dejamos simple:
            echo "Todos los campos son obligatorios.";
            return;
        }

        if ($user->findByEmail($data['correo'])) {
            echo "El correo ya está registrado.";
            return;
        }
        $data['contrasena'] = password_hash($data['contrasena'], PASSWORD_DEFAULT);

        
        // Crear el usuario
        if ($user->create($data)) {
            // Iniciar la sesión del usuario (es mejor guardar el ID)
            $newUser = $user->findByEmail($data['correo']);
            $_SESSION['usuario_id'] = $newUser['id'];
            $_SESSION['usuario_nombre'] = $newUser['nombre'];
            
            // Redirigir a la siguiente página
            header('Location: ' . url('/habitos-form')); // O a /habitos, como prefieras
            exit;
        } else {
            echo "Hubo un error al registrar.";
        }
    }
}
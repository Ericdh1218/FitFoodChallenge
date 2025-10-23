<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProgresoService;
use App\Models\User;
use App\Models\MedidasRegistro;
use App\Models\HabitoRegistro;

class ProgresoController
{
    /**
     * Muestra el dashboard de progreso y check-in
     */
    public function index()
    {
        // 1. Proteger la ruta
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $userId = $_SESSION['usuario_id'];
        $userModel = new User();
        
        // 2. Buscar los datos del usuario (para saber sus objetivos)
        $usuario = $userModel->findById($userId);
        
        // 3. (Añadiremos esto después)
        // Buscar si ya tiene un registro de hábitos para HOY
        // $registroHoy = ...

        view('home/progreso', [
            'title'   => 'Mi Progreso',
            'usuario' => $usuario
            // 'registroHoy' => $registroHoy (próximamente)
        ]);
    }

    public function store()
    {
        // 1. Verificar sesión
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        
        $userId = $_SESSION['usuario_id'];
        $nuevoPeso = (float)($_POST['peso'] ?? 0);
        $fechaHoy = date('Y-m-d');

        if ($nuevoPeso <= 0) {
            // Error, redirigir
            header('Location: ' . url('/micuenta'));
            exit;
        }

        // 2. Obtener la altura del usuario para recalcular el IMC
        $userModel = new User();
        $usuario = $userModel->findById($userId);
        $altura = (float)($usuario['altura'] ?? 0);

        $nuevoImc = 0;
        if ($altura > 0) {
            $altura_m = $altura / 100;
            $nuevoImc = round($nuevoPeso / ($altura_m * $altura_m), 1);
        }

        // 3. Guardar en el historial (medidas_registro)
        $medidasModel = new MedidasRegistro();
        $medidasModel->create($userId, $fechaHoy, $nuevoPeso);
        
        // 4. Actualizar el perfil actual (users)
        $userModel->updateBiometrics($userId, $nuevoPeso, $nuevoImc);

        // 5. Redirigir de vuelta a la página de perfil
        header('Location: ' . url('/micuenta'));
        exit;
    }
public function saveCheckin()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $userId = $_SESSION['usuario_id'];
        $fechaHoy = date('Y-m-d');
        
        $data = [
            'agua_cumplido'          => isset($_POST['agua_cumplido']) ? 1 : 0,
            'sueno_cumplido'         => isset($_POST['sueno_cumplido']) ? 1 : 0,
            'entrenamiento_cumplido' => isset($_POST['entrenamiento_cumplido']) ? 1 : 0,
        ];
        
        $model = new HabitoRegistro();
        $model->saveCheckin($userId, $fechaHoy, $data);

        // Redirigir de vuelta a la página de progreso
        header('Location: ' . url('/progreso'));
        exit;
    }
}


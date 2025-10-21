<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProgresoService;
use App\Models\User;
use App\Models\MedidasRegistro;

class ProgresoController extends Controller
{
    public function index(): void
    {
        $title = 'Mi progreso';
        $this->render('home/progreso', compact('title'));
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
}

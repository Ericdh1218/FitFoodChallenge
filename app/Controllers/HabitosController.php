<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
class HabitosController extends Controller
{
    /**
     * Muestra la página principal de hábitos
     */
    public function index(): void
    {
        $title = 'Hábitos';
        $this->render('home/habitos', compact('title'));
    }

    /**
     * NUEVO MÉTODO: Muestra el formulario para registrar hábitos
     * Esto responderá a la ruta /habitos-form
     */
    public function create(): void
    {
        $title = 'Configura tus Hábitos';

        // Asumimos que la vista se llamará 'habitos_form.php'
        // y está en 'app/Views/home/'
        $this->render('home/habitos_form', compact('title'));
    }

    // ... en app/Controllers/HabitosController.php
    public function store(): void
    {
        // 1. Asegurarnos de que el usuario esté logueado
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $userId = $_SESSION['usuario_id'];

        // 2. Recolectar los datos
        $data = [
            'nivel_actividad' => $_POST['nivel_actividad'] ?? 'sedentario',
            'objetivo_principal' => $_POST['objetivo_principal'] ?? '',
            'nivel_alimentacion' => $_POST['nivel_alimentacion'] ?? 'novato',
            'horas_sueno' => (int) ($_POST['horas_sueno'] ?? 0),
            'consumo_agua' => (int) ($_POST['consumo_agua'] ?? 0),
            // === NUEVOS CAMPOS ===
            'peso' => (float) ($_POST['peso'] ?? 0),
            'altura' => (float) ($_POST['altura'] ?? 0),
        ];

        // 3. Calcular el IMC
        $imc = 0;
        if ($data['altura'] > 0 && $data['peso'] > 0) {
            $altura_m = $data['altura'] / 100; // Convertir cm a metros
            // IMC = peso / (altura * altura)
            $imc = round($data['peso'] / ($altura_m * $altura_m), 1);
        }
        $data['imc'] = $imc;

        // 4. Actualizar el usuario en la BD
        $userModel = new User();
        $userModel->updateProfile($userId, $data);

        // 5. Redirigir al dashboard principal
        header('Location: ' . url('/'));
        exit;
    }
}
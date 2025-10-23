<?php
namespace App\Controllers;

use App\Models\Receta;
use App\Models\User; 
use App\Services\NutricionService; // <-- 1. Importa el Service

class GuiaNutricionalController
{
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
         
        $userId = $_SESSION['usuario_id'];
        $userModel = new User();
        $usuario = $userModel->findById($userId); 
        // === 2. LLAMA AL SERVICE AQUÍ ===
        // Usamos :: porque el método es static
        $estimacion = NutricionService::calcularEstimacionNutricional($usuario ?? []);
        // ===================================
        
        $recetaModel = new Receta();
        $categoriasDeseadas = [ /* ... */ ];
        $ejemplos = $recetaModel->getEjemplosPorCategorias($categoriasDeseadas, 3);

        view('home/guia_nutricional', [
            'title'    => 'Guía Nutricional',
            'ejemplos' => $ejemplos,
            'usuario'  => $usuario, 
            'estimacion' => $estimacion // <-- 3. Pasa la estimación a la vista
        ]);
    }
}
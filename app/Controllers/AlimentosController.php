<?php
namespace App\Controllers;

use App\Services\FdcApiService; // Importa el servicio

class AlimentosController // O el nombre que elijas
{
    /**
     * Endpoint para buscar alimentos (usado por AJAX).
     * Responde a: GET /alimentos/buscar?q=...
     */
    public function index()
    {
        // Proteger ruta si es necesario
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        // Simplemente carga la vista 'alimentos.php'
        view('home/alimentos', ['title' => 'Buscar Alimentos']);
    }
    // ==
    public function buscar()
    {
        header('Content-Type: application/json'); // Siempre devolverá JSON

        // Verificar si el usuario está logueado (importante si la búsqueda es privada)
        if (!isset($_SESSION['usuario_id'])) {
           echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
           exit;
        }

        $query = trim($_GET['q'] ?? '');
        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Término de búsqueda vacío.']);
            exit;
        }

        $fdcService = new FdcApiService();
        $resultados = $fdcService->searchFoods($query, 15); // Busca hasta 15 resultados

        if ($resultados === null) {
            echo json_encode(['success' => false, 'message' => 'Error al buscar alimentos. Intenta más tarde.']);
        } else {
            echo json_encode(['success' => true, 'foods' => $resultados]);
        }
        exit;
    }

    // --- AQUÍ AÑADIREMOS show($fdcId) DESPUÉS ---
}
<?php
namespace App\Controllers;

use App\Models\Receta;

class RecetasController
{
    /**
     * Muestra la lista de recetas (con filtros)
     * Responde a: /recetas
     */
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $model = new Receta();
        
        // === CORRECCIÓN AQUÍ ===
        // Añadimos los nuevos filtros a la lista que recogemos de $_GET
        $filtros = [
            'q'                => $_GET['q'] ?? null,
            'categoria'        => $_GET['categoria'] ?? null,
            'es_barato'        => $_GET['es_barato'] ?? null, // Nuevo
            'es_rapido'        => $_GET['es_rapido'] ?? null, // Nuevo
            'es_snack_estudio' => $_GET['es_snack_estudio'] ?? null, // Nuevo
        ];
        // =======================

        // El modelo ya sabe qué hacer con estos filtros si no son null
        $recetas = $model->all($filtros); 
        $categorias = $model->getCategorias();

        view('home/recetas', [
            'title'      => 'Recetario',
            'recetas'    => $recetas,
            'categorias' => $categorias,
            'filtros'    => $filtros // Pasamos todos los filtros a la vista
        ]);
    }

    /**
     * Muestra el detalle de UNA receta
     * Responde a: /receta?id=...
     */
    public function show()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: ' . url('/recetas'));
            exit;
        }

        $model = new Receta();
        $receta = $model->find($id);

        if (!$receta) {
            header('Location: ' . url('/recetas'));
            exit;
        }

        view('home/receta_detalle', [
            'title'  => $receta['titulo'],
            'receta' => $receta
        ]);
    }
}
<?php
namespace App\Controllers;

use App\Models\Articulo;

class ArticulosController
{
    /**
     * Muestra la lista de artículos.
     * Responde a: /articulos
     */
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login')); exit;
        }

        $model = new Articulo();
        $categoriaFiltro = $_GET['categoria'] ?? null;

        $articulos = $model->all($categoriaFiltro);
        $categorias = $model->getCategorias();

        view('home/articulos', [
            'title' => 'Biblioteca de Conocimiento',
            'articulos' => $articulos,
            'categorias' => $categorias,
            'filtroActual' => $categoriaFiltro
        ]);
    }

    /**
     * Muestra el detalle de UN artículo.
     * Responde a: /articulo?id=...
     */
    public function show()
    {
         if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login')); exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: ' . url('/articulos')); exit;
        }

        $model = new Articulo();
        $articulo = $model->find($id);

        if (!$articulo) {
            header('Location: ' . url('/articulos')); exit;
        }

        // Aquí podríamos marcar como leído automáticamente o añadir botón
        
        view('home/articulo_detalle', [
            'title' => $articulo['titulo'],
            'articulo' => $articulo
        ]);
    }
}
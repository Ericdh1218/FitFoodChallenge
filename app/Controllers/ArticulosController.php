<?php
namespace App\Controllers;

use App\Models\Articulo;
use App\Models\User; // Asegúrate que User esté importado si aún no lo estaba

class ArticulosController
{
    /**
     * Muestra la lista de artículos.
     * Responde a: /articulos
     */
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        $userId = $_SESSION['usuario_id'];

        $model = new Articulo();
        $categoriaFiltro = $_GET['categoria'] ?? null;

        $articulos = $model->all($categoriaFiltro);
        $categorias = $model->getCategorias();
        $articulosLeidosIds = $model->getReadArticleIds($userId);

        view('home/articulos', [
            'title' => 'Biblioteca de Conocimiento',
            'articulos' => $articulos,
            'categorias' => $categorias,
            'filtroActual' => $categoriaFiltro,
            'articulosLeidosIds' => $articulosLeidosIds
        ]);
    }

    /**
     * ==========================================
     * MÉTODO SHOW (ASEGÚRATE QUE ESTÉ PRESENTE)
     * ==========================================
     * Muestra el detalle de UN artículo.
     * Responde a: /articulo?id=...
     */
    public function show()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        $userId = $_SESSION['usuario_id'];

        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: ' . url('/articulos'));
            exit;
        }

        $model = new Articulo();
        $articulo = $model->find($id); // Usa el método find()

        if (!$articulo) {
            header('Location: ' . url('/articulos'));
            exit;
        }

        // Verificar si ya fue leído
        $yaLeido = $model->hasRead($userId, $id);

        // Manejar mensaje flash
        $mensajeFlash = null;
        if (isset($_SESSION['flash_message'])) {
            $mensajeFlash = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
        }

        // Llama a la vista de detalle
        view('home/articulo_detalle', [
    'title' => $articulo['titulo'], // <-- Usa sintaxis de array otra vez
    'articulo' => $articulo,
    'yaLeido' => $yaLeido,
    'mensajeFlash' => $mensajeFlash
]);
    } // <-- Fin del método show()


    /**
     * Procesa marcar un artículo como leído.
     * Responde a: POST /articulo/marcar-leido
     */
    public function markAsRead()
    {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(403);
            exit("Acceso denegado.");
        }
        $userId = $_SESSION['usuario_id'];
        $articuloId = (int) ($_POST['articulo_id'] ?? 0);

        if (!$articuloId) {
            http_response_code(400);
            exit("ID de artículo no válido.");
        }

        $model = new Articulo();
        $model->markAsRead($userId, $articuloId);

        $_SESSION['flash_message'] = "¡Artículo marcado como leído!";

        // Vuelve a la página del artículo detalle
        header('Location: ' . url('/articulo?id=' . $articuloId));
        exit;
    }

} // <-- Fin de la clase ArticulosController
<?php
namespace App\Controllers\Admin;

use App\Core\AdminMiddleware;
use App\Models\Receta;

class AdminRecetasController
{
    protected $recetaModel;

    public function __construct()
    {
        AdminMiddleware::check(); // ¡Proteger!
        $this->recetaModel = new Receta();
    }

    /**
     * Muestra la lista de todas las recetas.
     * Responde a: GET /admin/recetas
     */
    public function index()
    {
        $recetas = $this->recetaModel->getAllAdmin();
        
        view('admin/recetas/index', [
            'title' => 'Gestionar Recetas',
            'recetas' => $recetas
        ], 'admin');
    }

    /**
     * Muestra el formulario para editar una receta.
     * Responde a: GET /admin/recetas/edit?id=...
     */
    public function edit()
    {
        $recetaId = (int)($_GET['id'] ?? 0);
        $receta = $this->recetaModel->find($recetaId);
        if (!$receta) {
             header('Location: ' . url('/admin/recetas')); exit;
        }
        
        view('admin/recetas/edit', [
            'title' => 'Editar Receta',
            'receta' => $receta
        ], 'admin');
    }

    /**
     * Procesa la actualización de una receta.
     * Responde a: POST /admin/recetas/update
     */
    public function update()
    {
        $recetaId = (int)($_POST['receta_id'] ?? 0);
        $data = [
            'titulo' => $_POST['titulo'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? null,
            'ingredientes' => $_POST['ingredientes'] ?? null, // Texto simple
            'instrucciones' => $_POST['instrucciones'] ?? null,
            'categoria' => $_POST['categoria'] ?? 'comida de descanso',
            'es_barato' => isset($_POST['es_barato']) ? 1 : 0, // Checkbox
            'es_rapido' => isset($_POST['es_rapido']) ? 1 : 0,
            'es_snack_estudio' => isset($_POST['es_snack_estudio']) ? 1 : 0,
            'kcal_manual' => (int)($_POST['kcal_manual'] ?? 0),
            'proteinas_g_manual' => (float)($_POST['proteinas_g_manual'] ?? 0),
            'grasas_g_manual' => (float)($_POST['grasas_g_manual'] ?? 0),
            'carbos_g_manual' => (float)($_POST['carbos_g_manual'] ?? 0),
            'fibra_g_manual' => (float)($_POST['fibra_g_manual'] ?? 0)
        ];

        if (!$recetaId || empty($data['titulo'])) {
            $_SESSION['flash_message'] = 'Error: Faltan datos.';
            header('Location: ' . url('/admin/recetas/edit?id=' . $recetaId));
            exit;
        }
        
        $this->recetaModel->updateRecipeAsAdmin($recetaId, $data);
        
        $_SESSION['flash_message'] = '¡Receta actualizada con éxito!';
        header('Location: ' . url('/admin/recetas'));
        exit;
    }

    /**
     * Procesa la eliminación de una receta.
     * Responde a: POST /admin/recetas/delete
     */
    public function delete()
    {
        $recetaId = (int)($_POST['receta_id'] ?? 0);
        $this->recetaModel->deleteRecipe($recetaId);
        
        $_SESSION['flash_message'] = '¡Receta eliminada!';
        header('Location: ' . url('/admin/recetas'));
        exit;
    }
}
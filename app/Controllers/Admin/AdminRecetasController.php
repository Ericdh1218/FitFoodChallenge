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
        $receta = new Receta();
        $pred   = $receta->getPredefinidas();
        $users  = $receta->getDeUsuarios();

        view('admin/recetas/index', [
            'title'         => 'Gestionar Recetas',
            'predefinidas'  => $pred,
            'deUsuarios'    => $users
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

    public function create()
    {
        // tipo: pred | user
        $tipo = ($_GET['tipo'] ?? 'pred') === 'user' ? 'user' : 'pred';
        
        // (Si quieres un form separado para user/pred puedes pasarlo aquí)
        view('admin/recetas/create', [
            'title' => $tipo === 'pred' ? 'Nueva Receta (Predefinida)' : 'Nueva Receta de Usuario',
            'tipo'  => $tipo
        ], 'admin');
    }

   public function store()
    {
        $tipo = ($_GET['tipo'] ?? 'pred') === 'user' ? 'user' : 'pred';
        $recetaModel = new Receta();

        // 1) Subir imagen (opcional)
        $rutaRelativaImagen = $this->guardarUpload('imagen'); // devuelve 'img/recetas/xxx.png' o null

        // 2) Construir datos comunes
        $data = [
            'titulo'        => trim($_POST['titulo'] ?? ''),
            'categoria'     => $_POST['categoria'] ?? null,
            'descripcion'   => $_POST['descripcion'] ?? null,
            'instrucciones' => $_POST['instrucciones'] ?? null,
            'kcal'          => $_POST['kcal'] ?? null,
            'prote'         => $_POST['prote'] ?? null,
            'carbs'         => $_POST['carbs'] ?? null,
            'grasas'        => $_POST['grasas'] ?? null,
            // IMPORTANTE: usa el nombre de columna correcto de tu tabla
            // Tu tabla se llama 'imagen', así que:
            'imagen'        => $rutaRelativaImagen,   // <-- si tu modelo espera 'imagen'
            // Si tu modelo todavía usa 'imagen_url', cambia la clave a 'imagen_url'
        ];

        // 3) Guardar según tipo
        if ($tipo === 'user') {
            $userId = (int)($_POST['user_id'] ?? 0);
            // si tu método de modelo es createDeUsuario y espera 'imagen_url', ajusta la clave
            $ok = $recetaModel->createDeUsuario($data, $userId);
        } else {
            $ok = $recetaModel->createPredefinida($data);
        }

        if ($ok) {
            header('Location: ' . url('/admin/recetas'));
            exit;
        }

        // Si algo falla, vuelve al form
        $_SESSION['flash_message'] = 'No se pudo guardar la receta.';
        header('Location: ' . url('/admin/recetas/create?tipo=' . $tipo));
        exit;
    }

    /** Duplica una receta predefinida a una del usuario (acción rápida) */
    public function clonar()
    {
        $recetaId = (int)($_POST['receta_id'] ?? 0);
        $userId   = (int)($_POST['user_id']   ?? 0);
        if (!$recetaId || !$userId) {
            $_SESSION['flash_message'] = 'Datos incompletos para clonar.';
            header('Location: ' . url('/admin/recetas')); exit;
        }
        $M = new Receta();
        $ok = $M->clonarPredefinidaParaUsuario($recetaId, $userId);

        $_SESSION['flash_message'] = $ok ? 'Receta clonada al usuario.' : 'No se pudo clonar.';
        header('Location: ' . url('/admin/recetas')); exit;
    }

    public function delete()
    {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { header('Location: ' . url('/admin/recetas')); exit; }
        $M = new Receta();
        $M->delete($id);
        $_SESSION['flash_message'] = 'Receta eliminada.';
        header('Location: ' . url('/admin/recetas')); exit;
    }
}
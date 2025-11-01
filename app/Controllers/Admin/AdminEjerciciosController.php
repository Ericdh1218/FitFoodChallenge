<?php
namespace App\Controllers\Admin;

use App\Core\AdminMiddleware;
use App\Models\Ejercicio;

class AdminEjerciciosController
{
    public function __construct()
    {
        AdminMiddleware::check(); // ¡Proteger!
    }

    /**
     * Muestra la lista de todos los ejercicios.
     * Responde a: GET /admin/ejercicios
     */
    public function index()
    {
        // Usamos ::all() sin filtros para traer todo
        $ejercicios = Ejercicio::all(); 
        
        view('admin/ejercicios/index', [
            'title' => 'Gestionar Ejercicios',
            'ejercicios' => $ejercicios
        ], 'admin');
    }

    /**
     * Muestra el formulario para crear un nuevo ejercicio.
     * Responde a: GET /admin/ejercicios/create
     */
    public function create()
    {
        // Pasa las listas de filtros para los <select>
        view('admin/ejercicios/create', [
            'title' => 'Crear Nuevo Ejercicio',
            'grupos' => Ejercicio::grupos(),
            'tipos' => Ejercicio::tipos(),
            'equipos' => Ejercicio::equipos()
        ], 'admin');
    }

    /**
     * Procesa la creación de un nuevo ejercicio.
     * Responde a: POST /admin/ejercicios/store
     */
    public function store()
    {
        $data = $this->getDatosDelFormulario();
        
        if (empty($data['nombre']) || empty($data['grupo_muscular'])) {
            $_SESSION['flash_message'] = 'Error: Nombre y Grupo Muscular son obligatorios.';
            // Deberíamos pasar los datos de vuelta a la vista create
            header('Location: ' . url('/admin/ejercicios/create'));
            exit;
        }
        
        Ejercicio::create($data);
        
        $_SESSION['flash_message'] = '¡Ejercicio creado con éxito!';
        header('Location: ' . url('/admin/ejercicios'));
        exit;
    }

    /**
     * Muestra el formulario para editar un ejercicio.
     * Responde a: GET /admin/ejercicios/edit?id=...
     */
    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $ejercicio = Ejercicio::find($id);
        if (!$ejercicio) {
             header('Location: ' . url('/admin/ejercicios')); exit;
        }
        
        view('admin/ejercicios/edit', [
            'title' => 'Editar Ejercicio',
            'ejercicio' => $ejercicio,
            'grupos' => Ejercicio::grupos(),
            'tipos' => Ejercicio::tipos(),
            'equipos' => Ejercicio::equipos()
        ], 'admin');
    }

    /**
     * Procesa la actualización de un ejercicio.
     * Responde a: POST /admin/ejercicios/update
     */
    public function update()
    {
        $id = (int)($_POST['ejercicio_id'] ?? 0);
        $data = $this->getDatosDelFormulario();

        if (!$id || empty($data['nombre']) || empty($data['grupo_muscular'])) {
            $_SESSION['flash_message'] = 'Error: Faltan datos.';
            header('Location: ' . url('/admin/ejercicios/edit?id=' . $id));
            exit;
        }
        
        Ejercicio::update($id, $data);
        
        $_SESSION['flash_message'] = '¡Ejercicio actualizado con éxito!';
        header('Location: ' . url('/admin/ejercicios'));
        exit;
    }

    /**
     * Procesa la eliminación de un ejercicio.
     * Responde a: POST /admin/ejercicios/delete
     */
    public function delete()
    {
        $id = (int)($_POST['ejercicio_id'] ?? 0);
        Ejercicio::delete($id);
        
        $_SESSION['flash_message'] = '¡Ejercicio eliminado!';
        header('Location: ' . url('/admin/ejercicios'));
        exit;
    }
    
    /**
     * Helper privado para recoger datos del formulario (create y update)
     */
    private function getDatosDelFormulario(): array
    {
        // (Aquí podríamos añadir lógica de subida de imagen para 'media_url')
        
        return [
            'nombre' => $_POST['nombre'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? null,
            'media_url' => $_POST['media_url'] ?? null, // Nombre de archivo (ej. sentadilla.jpg)
            'video_url' => $_POST['video_url'] ?? null,
            'grupo_muscular' => $_POST['grupo_muscular'] ?? null,
            'tipo_entrenamiento' => $_POST['tipo_entrenamiento'] ?? null,
            'equipamiento' => $_POST['equipamiento'] ?? null
        ];
    }
}
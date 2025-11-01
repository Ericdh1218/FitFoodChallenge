<?php
namespace App\Controllers\Admin;

use App\Core\AdminMiddleware;
use App\Models\Rutina;
use App\Models\Ejercicio; // Necesario para listar ejercicios

class AdminRutinasController
{
    protected $rutinaModel;

    public function __construct()
    {
        AdminMiddleware::check(); // ¡Proteger!
        $this->rutinaModel = new Rutina();
    }

    /**
     * Muestra la lista de todas las rutinas.
     * Responde a: GET /admin/rutinas
     */
    public function index()
    {
        $rutinas = $this->rutinaModel->getAllAdmin();
        
        view('admin/rutinas/index', [
            'title' => 'Gestionar Rutinas',
            'rutinas' => $rutinas
        ], 'admin');
    }

    /**
     * Muestra el formulario para crear una nueva rutina predefinida.
     * Responde a: GET /admin/rutinas/create
     */
    public function create()
    {
        view('admin/rutinas/create', [
            'title' => 'Crear Nueva Rutina Predefinida'
        ], 'admin');
    }

    /**
     * Procesa la creación de una nueva rutina.
     * Responde a: POST /admin/rutinas/store
     */
    public function store()
    {
        $data = [
            'nombre_rutina' => $_POST['nombre_rutina'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? null,
            'nivel' => $_POST['nivel'] ?? 'principiante',
            'tipo_rutina' => $_POST['tipo_rutina'] ?? null
        ];

        if (empty($data['nombre_rutina'])) {
            $_SESSION['flash_message'] = 'Error: El nombre es obligatorio.';
            header('Location: ' . url('/admin/rutinas/create'));
            exit;
        }
        
        $this->rutinaModel->createPredefinida($data);
        
        $_SESSION['flash_message'] = '¡Rutina creada con éxito!';
        header('Location: ' . url('/admin/rutinas'));
        exit;
    }

    /**
     * Muestra el formulario para editar una rutina (detalles y ejercicios).
     * Responde a: GET /admin/rutinas/edit?id=...
     */
    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        // Usamos findPredefinida para asegurar que traiga todos los campos
        $rutina = $this->rutinaModel->findPredefinida($id); 
        if (!$rutina) {
             header('Location: ' . url('/admin/rutinas')); exit;
        }
        
        // Obtener los ejercicios que YA tiene esta rutina
        $ejerciciosEnRutina = $this->rutinaModel->getEjerciciosDeRutinaPredefinida($id);
        
        // Obtener TODOS los ejercicios disponibles para añadir
        $ejerciciosDisponibles = Ejercicio::all();
        
        view('admin/rutinas/edit', [
            'title' => 'Editar Rutina',
            'rutina' => $rutina,
            'ejerciciosEnRutina' => $ejerciciosEnRutina,
            'ejerciciosDisponibles' => $ejerciciosDisponibles
        ], 'admin');
    }

    /**
     * Procesa la actualización de los detalles de una rutina.
     * Responde a: POST /admin/rutinas/update
     */
    public function update()
    {
        $id = (int)($_POST['rutina_id'] ?? 0);
        $data = [
            'nombre_rutina' => $_POST['nombre_rutina'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? null,
            'nivel' => $_POST['nivel'] ?? 'principiante',
            'tipo_rutina' => $_POST['tipo_rutina'] ?? null
        ];

        if (!$id || empty($data['nombre_rutina'])) { /* ... manejo error ... */ }
        
        $this->rutinaModel->updateRutina($id, $data);
        
        $_SESSION['flash_message'] = '¡Rutina actualizada!';
        header('Location: ' . url('/admin/rutinas/edit?id=' . $id)); // Vuelve a la edición
        exit;
    }

    /**
     * Procesa la eliminación de una rutina.
     * Responde a: POST /admin/rutinas/delete
     */
    public function delete()
    {
        $id = (int)($_POST['rutina_id'] ?? 0);
        $this->rutinaModel->deleteRutina($id);
        
        $_SESSION['flash_message'] = '¡Rutina eliminada!';
        header('Location: ' . url('/admin/rutinas'));
        exit;
    }
    // En app/Controllers/Admin/AdminRutinasController.php

/** Procesa añadir un ejercicio a una rutina */
public function addEjercicio()
{
    $rutinaId = (int)($_POST['rutina_id'] ?? 0);
    $ejercicioId = (int)($_POST['ejercicio_id'] ?? 0);
    $seriesReps = trim($_POST['series_reps'] ?? '');

    if ($rutinaId && $ejercicioId && !empty($seriesReps)) {
        $this->rutinaModel->addEjercicioPredefinido($rutinaId, $ejercicioId, $seriesReps);
        $_SESSION['flash_message'] = 'Ejercicio añadido.';
    } else {
        $_SESSION['flash_message'] = 'Error: Faltan datos para añadir el ejercicio.';
    }

    header('Location: ' . url('/admin/rutinas/edit?id=' . $rutinaId));
    exit;
}

/** Procesa quitar un ejercicio de una rutina */
public function removeEjercicio()
{
    $rutinaId = (int)($_POST['rutina_id'] ?? 0);
    $ejercicioId = (int)($_POST['ejercicio_id'] ?? 0);

    if ($rutinaId && $ejercicioId) {
        $this->rutinaModel->removeEjercicioPredefinido($rutinaId, $ejercicioId);
        $_SESSION['flash_message'] = 'Ejercicio quitado.';
    }

    header('Location: ' . url('/admin/rutinas/edit?id=' . $rutinaId));
    exit;
}
}
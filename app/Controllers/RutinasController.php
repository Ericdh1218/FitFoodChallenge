<?php
namespace App\Controllers;

use App\Models\Rutina;
use App\Models\Ejercicio;
class RutinasController
{
    /**
     * Muestra la lista de todas las rutinas prediseñadas.
     * Responde a la ruta: /rutinas
     */
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        $userId = $_SESSION['usuario_id']; // <-- Obtenemos el ID del usuario

        $rutinaModel = new Rutina();
        
        // === BUSCAMOS AMBOS TIPOS ===
        $rutinasPersonales = $rutinaModel->findByUserId($userId); // <-- Rutinas del usuario
        $rutinasPredefinidas = $rutinaModel->getPredefinidas();   // <-- Las que ya teníamos
        // ==========================

        view('home/rutinas', [
            'title' => 'Mis Rutinas', // Cambiamos el título
            'rutinasPersonales' => $rutinasPersonales,     // <-- Pasamos las personales
            'rutinasPredefinidas' => $rutinasPredefinidas // <-- Pasamos las predefinidas
        ]);
    }

    /**
     * Muestra el detalle de UNA rutina prediseñada.
     * Responde a la ruta: /rutina (usando ?id=)
     */
    public function show()
    {
        // Solo usuarios logueados
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: ' . url('/rutinas'));
            exit;
        }

        $rutinaModel = new Rutina();
        $rutina = $rutinaModel->findPredefinida($id);

        if (!$rutina) {
            // No se encontró la rutina, volver a la lista
            header('Location: ' . url('/rutinas'));
            exit;
        }

        $ejercicios = $rutinaModel->getEjerciciosDeRutinaPredefinida($id);

        view('home/rutina_detalle', [
            'rutina' => $rutina,
            'ejercicios' => $ejercicios
        ]);
    }
    public function create()
    {
        // Proteger la ruta
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        view('home/rutinas_crear', ['title' => 'Crear Nueva Rutina']);
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Guarda la nueva rutina (solo el nombre)
     * ==========================================
     */
    public function store()
    {
        // Proteger la ruta
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $userId = $_SESSION['usuario_id'];
        $nombreRutina = trim($_POST['nombre_rutina'] ?? '');

        // Validación simple
        if (empty($nombreRutina)) {
            // Podríamos mostrar un error en la vista
            view('home/rutinas_crear', [
                'title' => 'Crear Nueva Rutina', 
                'error' => 'El nombre de la rutina es obligatorio.'
            ]);
            return;
        }

        $rutinaModel = new Rutina();
        
        // Llamamos a un nuevo método en el modelo para guardar
        $nuevaRutinaId = $rutinaModel->createPersonal($userId, $nombreRutina);

       if ($nuevaRutinaId) {
            // Éxito: Redirigir a la página para añadir ejercicios
            header('Location: ' . url('/rutina/editar?id=' . $nuevaRutinaId));// <-- CAMBIO AQUÍ
            exit;
        } else {
            // Error al guardar
             view('home/rutinas_crear', [
                'title' => 'Crear Nueva Rutina', 
                'error' => 'Hubo un error al guardar la rutina. Inténtalo de nuevo.'
            ]);
            return;
        }
    }
    /**
     * ==========================================
     * NUEVO MÉTODO: Muestra la página para editar una rutina personal
     * (añadir/quitar ejercicios)
     * ==========================================
     */
    public function edit()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $userId = $_SESSION['usuario_id'];
        $rutinaId = (int)($_GET['id'] ?? 0);

        if (!$rutinaId) {
            header('Location: ' . url('/rutinas')); // Si no hay ID, volver
            exit;
        }

        $rutinaModel = new Rutina();
        // Usaremos una nueva función para buscar rutinas personales
        $rutina = $rutinaModel->findPersonal($rutinaId, $userId);
        if (!$rutina) { /* ... manejo de error ... */ }

        $ejerciciosEnRutina = $rutinaModel->getEjerciciosDeRutinaPersonal($rutinaId);
        
        // === NUEVO: MODO EDICIÓN ===
        $editMode = isset($_GET['modo']) && $_GET['modo'] === 'editar';
        // ==========================

        // Solo cargamos TODOS los ejercicios si estamos en modo edición
        $ejerciciosDisponibles = [];
        if ($editMode) {
            $ejercicioModel = new Ejercicio();
            $ejerciciosDisponibles = $ejercicioModel->all(); 
        }

        view('home/rutinas_editar', [
            'title'                 => ($editMode ? 'Editando: ' : 'Viendo: ') . $rutina['nombre_rutina'],
            'rutina'                => $rutina,
            'ejerciciosEnRutina'    => $ejerciciosEnRutina,
            'ejerciciosDisponibles' => $ejerciciosDisponibles,
            'editMode'              => $editMode // <-- Pasar el modo a la vista
        ]);
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Procesa la adición de un ejercicio a una rutina personal
     * ==========================================
     */
    public function addEjercicio() // Ya no es void, devuelve JSON
    {
        // Headers para respuesta JSON
        header('Content-Type: application/json');

        // Función helper para devolver JSON y terminar
        $respondJson = function($data) {
            echo json_encode($data);
            exit;
        };

        if (!isset($_SESSION['usuario_id'])) {
            return $respondJson(['success' => false, 'message' => 'Acceso denegado.']);
        }

        $userId = $_SESSION['usuario_id'];
        $rutinaId = (int)($_POST['rutina_id'] ?? 0);
        $ejercicioId = (int)($_POST['ejercicio_id'] ?? 0);
        // === NUEVOS CAMPOS ===
        $series = $_POST['series'] ?? null;
        $repeticiones = $_POST['repeticiones'] ?? null;
        // =====================

        if (!$rutinaId || !$ejercicioId || $series === null || $repeticiones === null) {
            return $respondJson(['success' => false, 'message' => 'Faltan datos (rutina, ejercicio, series o reps).']);
        }

        $rutinaModel = new Rutina();
        $rutina = $rutinaModel->findPersonal($rutinaId, $userId);
        if (!$rutina) {
            return $respondJson(['success' => false, 'message' => 'Rutina no encontrada o no te pertenece.']);
        }

        // === PASAR SERIES/REPS AL MODELO ===
        $exito = $rutinaModel->addEjercicioARutina($rutinaId, $ejercicioId, $series, $repeticiones);
        // ===================================

        if ($exito) {
            return $respondJson(['success' => true, 'message' => 'Ejercicio añadido.']);
        } else {
            return $respondJson(['success' => false, 'message' => 'Error al guardar en la base de datos.']);
        }
        // YA NO HAY REDIRECCIÓN AQUÍ
    }
    /**
     * ==========================================
     * NUEVO MÉTODO: Elimina una rutina personal
     * ==========================================
     */
    public function delete()
    {
        // 1. Proteger ruta y obtener datos
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(403); 
            echo "Acceso denegado.";
            exit;
        }

        $userId = $_SESSION['usuario_id'];
        $rutinaId = (int)($_POST['rutina_id'] ?? 0);

        if (!$rutinaId) {
            echo "ID de rutina no proporcionado."; // O redirigir con error
            return;
        }

        $rutinaModel = new Rutina();

        // 2. Verificar que la rutina pertenece al usuario (¡Muy importante!)
        $rutina = $rutinaModel->findPersonal($rutinaId, $userId);
        if (!$rutina) {
            http_response_code(404);
            echo "Rutina no encontrada o no te pertenece.";
            return;
        }

        // 3. Llamar al modelo para eliminar la rutina
        $exito = $rutinaModel->deletePersonal($rutinaId, $userId);

        // 4. Redirigir de vuelta a la lista de rutinas
        //    (Podríamos añadir un mensaje de éxito/error)
        header('Location: ' . url('/rutinas'));
        exit;
    }
    /**
     * ==========================================
     * NUEVO MÉTODO: Procesa la eliminación de un ejercicio de una rutina personal
     * ==========================================
     */
    public function removeEjercicio() // Devuelve JSON
    {
        header('Content-Type: application/json');
        $respondJson = fn($data) => exit(json_encode($data)); // Helper

        if (!isset($_SESSION['usuario_id'])) {
            return $respondJson(['success' => false, 'message' => 'Acceso denegado.']);
        }

        $userId = $_SESSION['usuario_id'];
        // ID de la fila en 'rutina_ejercicios' a borrar
        $rutinaEjercicioId = (int)($_POST['rutina_ejercicio_id'] ?? 0); 

        if (!$rutinaEjercicioId) {
            return $respondJson(['success' => false, 'message' => 'ID de relación no proporcionado.']);
        }

        $rutinaModel = new Rutina();

        // Verificar que el registro a borrar realmente pertenece a una rutina del usuario
        // (Usaremos una nueva función en el modelo para esto)
        if (!$rutinaModel->verificarPertenenciaEjercicioRutina($rutinaEjercicioId, $userId)) {
             return $respondJson(['success' => false, 'message' => 'No tienes permiso para eliminar este ejercicio.']);
        }

        // Llamar al modelo para eliminar la relación
        $exito = $rutinaModel->removeEjercicioFromRutina($rutinaEjercicioId);

        if ($exito) {
            return $respondJson(['success' => true, 'message' => 'Ejercicio quitado.']);
        } else {
            return $respondJson(['success' => false, 'message' => 'Error al quitar el ejercicio.']);
        }
    }
    /**
     * ==========================================
     * NUEVO MÉTODO: Muestra el detalle de UNA rutina personal
     * ==========================================
     */
    public function showPersonal()
    {
        // 1. Proteger y obtener IDs
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        $userId = $_SESSION['usuario_id'];
        $rutinaId = (int)($_GET['id'] ?? 0);

        if (!$rutinaId) {
            header('Location: ' . url('/rutinas')); // Volver si no hay ID
            exit;
        }

        $rutinaModel = new Rutina();
        
        // 2. Buscar la rutina (verificando que sea del usuario)
        $rutina = $rutinaModel->findPersonal($rutinaId, $userId);
        if (!$rutina) {
            echo "Rutina no encontrada o no te pertenece."; // O redirigir
            return;
        }

        // 3. Buscar los ejercicios de esa rutina
        $ejercicios = $rutinaModel->getEjerciciosDeRutinaPersonal($rutinaId);

        // 4. Mostrar la nueva vista
        view('home/mi_rutina_detalle', [
            'title'      => $rutina['nombre_rutina'],
            'rutina'     => $rutina,
            'ejercicios' => $ejercicios
        ]);
    }
}
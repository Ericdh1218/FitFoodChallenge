<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProgresoService;
use App\Models\User;
use App\Models\MedidasRegistro;
use App\Models\HabitoRegistro;
use App\Models\DesafioUsuario;
use App\Models\ProgresoFoto;
class ProgresoController
{
    /**
     * Muestra el dashboard de progreso y check-in
     */
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) { 
            header('Location: ' . url('/login')); exit; 
        }
        $userId = $_SESSION['usuario_id'];
        $fechaHoy = date('Y-m-d');

        // === INSTANCIAR MODELOS ===
        $userModel = new User();
        $habitoModel = new HabitoRegistro();
        $medidasModel = new MedidasRegistro();
        $fotoModel = new ProgresoFoto(); // <-- Nuevo
        // ==========================
        
        $usuario = $userModel->findById($userId);
        $registroHoy = $habitoModel->findByDate($userId, $fechaHoy);
        
        // === DATOS PARA GRÁFICAS ===
        // 1. Historial de Peso (para la gráfica)
        // (Necesitamos modificar getRecentHistory para que ordene ASC por fecha)
        $historialPeso = $medidasModel->getHistoryForChart($userId);
        $pesoDataJs = json_encode($historialPeso); // Convertir a JSON para JS

        // 2. Historial de Hábitos (para la gráfica)
        // (Necesitamos una nueva función en HabitoRegistro)
        $historialHabitos = $habitoModel->getHistoryForChart($userId, 30); // Últimos 30 días
        $habitosDataJs = json_encode($historialHabitos); // Convertir a JSON para JS
        // ==========================

        // === FOTOS DE PROGRESO ===
        $fotos = $fotoModel->findByUserId($userId);
        // =======================

        view('home/progreso', [
            'title'          => 'Mi Progreso',
            'usuario'        => $usuario,
            'registroHoy'    => $registroHoy,
            'historialHabitos' => $historialHabitos, // Para la lista (puedes quitarlo si no lo usas)
            'historialPeso'  => $historialPeso,  // Para la lista (puedes quitarlo)
            'pesoDataJs'     => $pesoDataJs,     // Para la gráfica
            'habitosDataJs'  => $habitosDataJs,  // Para la gráfica
            'fotos'          => $fotos           // Para la galería
        ]);
    }
    public function store()
    {
        // 1. Verificar sesión
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        
        $userId = $_SESSION['usuario_id'];
        $nuevoPeso = (float)($_POST['peso'] ?? 0);
        $fechaHoy = date('Y-m-d');

        if ($nuevoPeso <= 0) {
            // Error, redirigir
            header('Location: ' . url('/micuenta'));
            exit;
        }

        // 2. Obtener la altura del usuario para recalcular el IMC
        $userModel = new User();
        $usuario = $userModel->findById($userId);
        $altura = (float)($usuario['altura'] ?? 0);

        $nuevoImc = 0;
        if ($altura > 0) {
            $altura_m = $altura / 100;
            $nuevoImc = round($nuevoPeso / ($altura_m * $altura_m), 1);
        }

        // 3. Guardar en el historial (medidas_registro)
        $medidasModel = new MedidasRegistro();
        $medidasModel->create($userId, $fechaHoy, $nuevoPeso);
        
        // 4. Actualizar el perfil actual (users)
        $userModel->updateBiometrics($userId, $nuevoPeso, $nuevoImc);

        // 5. Redirigir de vuelta a la página de perfil
        header('Location: ' . url('/micuenta'));
        exit;
    }
public function saveCheckin()
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . url('/login'));
        exit;
    }

    $userId = $_SESSION['usuario_id'];
    $fechaHoy = date('Y-m-d');
    
    $data = [
        'agua_cumplido'          => isset($_POST['agua_cumplido']) ? 1 : 0,
        'sueno_cumplido'         => isset($_POST['sueno_cumplido']) ? 1 : 0,
        'entrenamiento_cumplido' => isset($_POST['entrenamiento_cumplido']) ? 1 : 0,
    ];
    
    // 1. Guardar el registro de hábito diario (como antes)
    $habitoModel = new HabitoRegistro();
    // (Asegúrate de que la función findByDate exista en HabitoRegistro)
    $registroPrevio = $habitoModel->findByDate($userId, $fechaHoy); 
    $habitoModel->saveCheckin($userId, $fechaHoy, $data);

    // 2. === NUEVA LÓGICA DE DESAFÍOS ===
    // Solo actualiza desafíos si este es el PRIMER check-in del día
    // para evitar que el usuario haga trampa (marcar/desmarcar/marcar)
    if (!$registroPrevio || $registroPrevio['completado_hoy'] == 0) { // Asumimos una columna 'completado_hoy' o similar
        // (Simplificación por ahora: actualizamos siempre que se marque)
        
        $desafioModel = new DesafioUsuario();

        // Si marcó la casilla de agua, actualiza retos de 'agua'
        if ($data['agua_cumplido']) {
            $desafioModel->actualizarProgreso($userId, 'agua');
        }
        // Si marcó la casilla de sueño, actualiza retos de 'sueno'
        if ($data['sueno_cumplido']) {
            $desafioModel->actualizarProgreso($userId, 'sueno');
        }
        // Si marcó la casilla de entrenamiento, actualiza retos de 'entrenamiento'
        if ($data['entrenamiento_cumplido']) {
            $desafioModel->actualizarProgreso($userId, 'entrenamiento');
        }
        // (Añadir 'alimentacion' aquí en el futuro)
    }
    // ===================================

    // Redirigir de vuelta a la página de progreso
    header('Location: ' . url('/progreso'));
    exit;
}

public function savePeso()
    {
        if (!isset($_SESSION['usuario_id'])) { /* ... */ }
        $userId = $_SESSION['usuario_id'];
        $nuevoPeso = (float)($_POST['peso'] ?? 0);
        $fechaHoy = date('Y-m-d');

        if ($nuevoPeso <= 0) { /* ... redirigir con error ... */ }

        $userModel = new User();
        $usuario = $userModel->findById($userId);
        $altura = (float)($usuario['altura'] ?? 0);

        $nuevoImc = 0;
        if ($altura > 0) { /* ... calcular IMC ... */ }

        $medidasModel = new MedidasRegistro();
        // Usaremos saveOrUpdate para evitar duplicados en el mismo día
        $medidasModel->saveOrUpdateByDate($userId, $fechaHoy, $nuevoPeso);
        
        $userModel->updateBiometrics($userId, $nuevoPeso, $nuevoImc);

        header('Location: ' . url('/progreso')); // Volver a progreso
        exit;
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Sube una foto de progreso
     * ==========================================
     */
    public function uploadPhoto()
    {
        if (!isset($_SESSION['usuario_id'])) { /* ... */ }
        $userId = $_SESSION['usuario_id'];
        $nota = trim($_POST['nota'] ?? '');
        $fecha = date('Y-m-d');

        // 1. Validar archivo
        if (isset($_FILES['foto_progreso']) && $_FILES['foto_progreso']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto_progreso'];
            $allowedTypes = ['image/jpeg', 'image/png'];
            $maxSize = 5 * 1024 * 1024; // 5 MB

            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                
                // 2. Generar nombre único y ruta
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $nombreArchivo = $userId . '_' . time() . '.' . $extension;
                $rutaDestino = BASE_PATH . '/public/assets/uploads/progreso/' . $nombreArchivo;
                $directorioDestino = dirname($rutaDestino);

                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0775, true); 
                }

                // 3. Mover archivo
                if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
                    // 4. Guardar en BD
                    $fotoModel = new ProgresoFoto();
                    $fotoModel->create($userId, $fecha, $nombreArchivo, $nota);
                    $_SESSION['flash_message'] = "¡Foto subida con éxito!";
                } else {
                    $_SESSION['flash_message'] = "Error al mover el archivo.";
                }
            } else {
                $_SESSION['flash_message'] = "Archivo inválido (tipo o tamaño > 5MB).";
            }
        } else {
            $_SESSION['flash_message'] = "No se subió ningún archivo o hubo un error.";
        }

        header('Location: ' . url('/progreso'));
        exit;
    }

    // En app/Controllers/ProgresoController.php
// (Asegúrate de tener: use App\Models\User; y use App\Models\HabitoRegistro;)

    // ... (index(), saveCheckin(), savePeso(), uploadPhoto() ... ) ...

    /**
     * ==========================================
     * NUEVO MÉTODO: Muestra el formulario para EDITAR el check-in de hoy
     * ==========================================
     */
    public function editCheckin()
    {
        if (!isset($_SESSION['usuario_id'])) { 
            header('Location: ' . url('/login')); exit; 
        }
        $userId = $_SESSION['usuario_id'];
        $fechaHoy = date('Y-m-d');

        // Modelos
        $habitoModel = new HabitoRegistro();
        $userModel = new User();

        // 1. Buscar el registro de hoy (para pre-llenar el formulario)
        $registroHoy = $habitoModel->findByDate($userId, $fechaHoy);
        
        // 2. Si por alguna razón no hay registro, redirige de vuelta
        if (!$registroHoy) {
            header('Location: ' . url('/progreso'));
            exit;
        }

        // 3. Buscar datos del usuario (para los textos de objetivos)
        $usuario = $userModel->findById($userId);

        // 4. Mostrar la nueva vista de edición
        view('home/progreso_checkin_editar', [
            'title'       => 'Editar Check-in',
            'usuario'     => $usuario,
            'registroHoy' => $registroHoy // Pasa los datos actuales
        ]);
    }
    public function deletePhoto()
    {
        header('Content-Type: application/json');
        $respondJson = fn($data) => exit(json_encode($data)); // Helper

        if (!isset($_SESSION['usuario_id'])) {
            return $respondJson(['success' => false, 'message' => 'Acceso denegado.']);
        }

        $userId = $_SESSION['usuario_id'];
        $fotoId = (int)($_POST['foto_id'] ?? 0); // Recibimos el ID de la foto

        if (!$fotoId) {
            return $respondJson(['success' => false, 'message' => 'ID de foto no válido.']);
        }

        $fotoModel = new ProgresoFoto();

        // 1. Verificar que la foto existe y pertenece al usuario
        $foto = $fotoModel->findByIdAndUser($fotoId, $userId);
        if (!$foto) {
            return $respondJson(['success' => false, 'message' => 'Foto no encontrada o no te pertenece.']);
        }
        
        $nombreArchivo = $foto['nombre_archivo'];
        $rutaArchivo = BASE_PATH . '/public/assets/uploads/progreso/' . $nombreArchivo;

        // 2. Eliminar el registro de la Base de Datos
        if ($fotoModel->deleteById($fotoId)) {
            // 3. Si se borró de la BD, borrar el archivo físico
            if (file_exists($rutaArchivo)) {
                @unlink($rutaArchivo); // @ para suprimir errores si el archivo no existe
            }
            return $respondJson(['success' => true, 'message' => 'Foto eliminada.']);
        } else {
            return $respondJson(['success' => false, 'message' => 'Error al eliminar la foto de la base de datos.']);
        }
    }
}


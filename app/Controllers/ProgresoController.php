<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProgresoService;
use App\Models\User;
use App\Models\MedidasRegistro;
use App\Models\HabitoRegistro;
use App\Models\DesafioUsuario;
use App\Models\ProgresoFoto;
use App\Services\BadgeService;
use App\Models\Insignia;    

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
        $fotoModel = new ProgresoFoto();
        $insigniaModel = new Insignia();
        // ==========================
        
        $usuario = $userModel->findById($userId); // Contiene $usuario['xp'] y $usuario['level']
        $registroHoy = $habitoModel->findByDate($userId, $fechaHoy);
        
        // ... (Datos para gráficas y fotos) ...
        $historialPeso = $medidasModel->getHistoryForChart($userId);
        $pesoDataJs = json_encode($historialPeso);
        $historialHabitos = $habitoModel->getHistoryForChart($userId, 30);
        $habitosDataJs = json_encode($historialHabitos);
        $fotos = $fotoModel->findByUserId($userId);

        $insignias = $insigniaModel->findInsigniasByUserId($userId);

        // === NUEVA LÓGICA DE GAMIFICACIÓN ===
        $nivelActualNum = $usuario['level'] ?? 1;
        $xpActual = $usuario['xp'] ?? 0;

        // Obtener info del nivel actual (nombre y XP de inicio)
        $infoNivelActual = $userModel->getLevelInfo($nivelActualNum);
        $nombreNivel = $infoNivelActual['nombre_nivel'] ?? 'Principiante';
        $xpInicioNivel = $infoNivelActual['xp_requerido'] ?? 0;
        
        // Obtener info del siguiente nivel (XP requerido)
        $xpSiguienteNivel = $userModel->getNextLevelXp($nivelActualNum);
        
        // Si no hay siguiente nivel (es nivel max), la barra se llena al 100%
        if ($xpSiguienteNivel === null) {
            $xpSiguienteNivel = $xpInicioNivel; // Evita división por cero
            $porcentajeProgreso = 100;
        } else {
            $xpTotalParaSubir = $xpSiguienteNivel - $xpInicioNivel;
            $xpEnEsteNivel = $xpActual - $xpInicioNivel;
            $porcentajeProgreso = ($xpTotalParaSubir > 0) ? ($xpEnEsteNivel / $xpTotalParaSubir) * 100 : 100;
        }
        // ======================================

        view('home/progreso', [
            'title'          => 'Mi Progreso',
            'usuario'        => $usuario,
            'registroHoy'    => $registroHoy,
            'historialHabitos' => $historialHabitos, 
            'historialPeso'  => $historialPeso,
            'pesoDataJs'     => $pesoDataJs,
            'habitosDataJs'  => $habitosDataJs,
            'fotos'          => $fotos,
            
            // --- NUEVOS DATOS PARA LA VISTA ---
            'nombreNivel'        => $nombreNivel,
            'xpActual'           => $xpActual,
            'xpSiguienteNivel'   => $xpSiguienteNivel,
            'xpInicioNivel'      => $xpInicioNivel,
            'porcentajeProgreso' => $porcentajeProgreso,
            'insignias'      => $insignias
            // --- FIN NUEVOS DATOS ---
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
// En app/Controllers/ProgresoController.php

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

        // Verifica si el usuario marcó al menos un hábito
        $habitosCompletados = $data['agua_cumplido'] || $data['sueno_cumplido'] || $data['entrenamiento_cumplido'];
        
        // 1. Guardar el registro de hábito diario
        $habitoModel = new HabitoRegistro();
        $registroPrevio = $habitoModel->findByDate($userId, $fechaHoy); // Verifica si ya existía un registro hoy
        $habitoModel->saveCheckin($userId, $fechaHoy, $data);

        // --- INICIO LÓGICA DE GAMIFICACIÓN Y DESAFÍOS ---
        
        $userModel = new User();
        $badgeService = new BadgeService();
        $desafioModel = new DesafioUsuario();

        $xpGanado = 0;
        $insigniaGanada = null; // Para insignias de una sola vez (ej. primer check-in)
        $insigniaDesafio = null; // Para insignias de desafío completado
        $subioDeNivel = false;
        $nombreNuevoNivel = '';

        // Solo procesa XP y desafíos si marcó algo Y si es la primera vez que guarda hoy
        // Esto evita ganar XP/progreso múltiples veces editando
        $esPrimerRegistroDelDia = !$registroPrevio || (
            $registroPrevio['agua_cumplido'] == 0 && 
            $registroPrevio['sueno_cumplido'] == 0 && 
            $registroPrevio['entrenamiento_cumplido'] == 0
        );

        if ($habitosCompletados && $esPrimerRegistroDelDia) {
            
            // 2. Otorgar XP por el check-in diario
            $xpPorCheckin = 10; 
            $resultadoXp = $userModel->addXp($userId, $xpPorCheckin);
            $xpGanado += $xpPorCheckin;
            if ($resultadoXp['subio_de_nivel']) {
                $subioDeNivel = true;
                $nombreNuevoNivel = $resultadoXp['nombre_nuevo_nivel'];
            }
            
            // 3. Otorgar insignia "Primer Check-in" (si es la primera vez en la historia)
            // (Necesitamos una comprobación mejor, ej. contar total de registros)
            // Por ahora, lo simplificamos:
            if (!$registroPrevio) { // Asume que si no hay registro *hoy*, podría ser el primero
                $insigniaGanada = $badgeService->otorgarInsigniaPorCodigo($userId, 'primer-checkin');
            }

            // 4. Actualizar progreso de Desafíos
            $tiposHabito = ['agua', 'sueno', 'entrenamiento'];
            foreach ($tiposHabito as $tipo) {
                if ($data[$tipo.'_cumplido']) {
                    // Actualiza el progreso
                    $desafioModel->actualizarProgreso($userId, $tipo);
                    
                    // Comprueba si ESE desafío se completó AHORA
                    $progreso = $desafioModel->findProgresoByHabito($userId, $tipo);
                    
                    if ($progreso && $progreso['completado'] == 1) {
                        // El desafío se acaba de completar, otorga la insignia
                        // (Asumimos que el código de insignia coincide, ej: 'reto-agua-5')
                        $codigoInsignia = 'reto-' . $tipo . '-' . $progreso['duracion_dias']; 
                        $insigniaDesafioGanada = $badgeService->otorgarInsigniaPorCodigo($userId, $codigoInsignia);
                        if ($insigniaDesafioGanada) {
                             $insigniaDesafio = $insigniaDesafioGanada;
                        }
                    }
                }
            }
        }
        
        // --- FIN LÓGICA DE GAMIFICACIÓN ---

        // 5. Crear Mensaje Flash
        $mensaje = "¡Check-in guardado!";
        if ($xpGanado > 0) {
            $mensaje = "¡Check-in guardado! Ganaste +{$xpGanado} XP.";
        }
        if ($subioDeNivel) {
            $mensaje = "¡FELICIDADES! Subiste a: Nivel {$nombreNuevoNivel}. Ganaste +{$xpGanado} XP.";
        }
        if ($insigniaGanada) { $mensaje .= " ¡Obtuviste la insignia '{$insigniaGanada}'!"; }
        if ($insigniaDesafio) { $mensaje .= " ¡Completaste un desafío y ganaste la insignia '{$insigniaDesafio}'!"; }
        
        $_SESSION['flash_message'] = $mensaje;

        // 6. Redirigir
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


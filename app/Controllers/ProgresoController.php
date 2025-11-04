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
use App\Models\Progreso; // ← IMPORTANTE: para addAgua/addMinutos/stats


class ProgresoController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) { 
            header('Location: ' . url('/login')); exit; 
        }
        $userId   = $_SESSION['usuario_id'];
        $fechaHoy = date('Y-m-d');

        $userModel     = new User();
        $habitoModel   = new HabitoRegistro();
        $medidasModel  = new MedidasRegistro();
        $fotoModel     = new ProgresoFoto();
        $insigniaModel = new Insignia();

        $usuario       = $userModel->findById($userId);
        $registroHoy   = $habitoModel->findByDate($userId, $fechaHoy);

        $historialPeso    = $medidasModel->getHistoryForChart($userId);
        $pesoDataJs       = json_encode($historialPeso);
        $historialHabitos = $habitoModel->getHistoryForChart($userId, 30);
        $habitosDataJs    = json_encode($historialHabitos);
        $fotos            = $fotoModel->findByUserId($userId);
        $insignias        = $insigniaModel->findInsigniasByUserId($userId);

        // Gamificación
        $nivelActualNum   = $usuario['level'] ?? 1;
        $xpActual         = $usuario['xp'] ?? 0;
        $infoNivelActual  = $userModel->getLevelInfo($nivelActualNum);
        $nombreNivel      = $infoNivelActual['nombre_nivel'] ?? 'Principiante';
        $xpInicioNivel    = $infoNivelActual['xp_requerido'] ?? 0;
        $xpSiguienteNivel = $userModel->getNextLevelXp($nivelActualNum);

        if ($xpSiguienteNivel === null) {
            $xpSiguienteNivel   = $xpInicioNivel;
            $porcentajeProgreso = 100;
        } else {
            $xpTotalParaSubir   = $xpSiguienteNivel - $xpInicioNivel;
            $xpEnEsteNivel      = $xpActual - $xpInicioNivel;
            $porcentajeProgreso = ($xpTotalParaSubir > 0) ? ($xpEnEsteNivel / $xpTotalParaSubir) * 100 : 100;
        }

        view('home/progreso', [
            'title'              => 'Mi Progreso',
            'usuario'            => $usuario,
            'registroHoy'        => $registroHoy,
            'historialHabitos'   => $historialHabitos, 
            'historialPeso'      => $historialPeso,
            'pesoDataJs'         => $pesoDataJs,
            'habitosDataJs'      => $habitosDataJs,
            'fotos'              => $fotos,
            'nombreNivel'        => $nombreNivel,
            'xpActual'           => $xpActual,
            'xpSiguienteNivel'   => $xpSiguienteNivel,
            'xpInicioNivel'      => $xpInicioNivel,
            'porcentajeProgreso' => $porcentajeProgreso,
            'insignias'          => $insignias
        ]);
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) { header('Location: ' . url('/login')); exit; }

        $userId    = $_SESSION['usuario_id'];
        $nuevoPeso = (float)($_POST['peso'] ?? 0);
        $fechaHoy  = date('Y-m-d');

        if ($nuevoPeso <= 0) { header('Location: ' . url('/micuenta')); exit; }

        $userModel = new User();
        $usuario   = $userModel->findById($userId);
        $altura    = (float)($usuario['altura'] ?? 0);

        $nuevoImc = 0;
        if ($altura > 0) {
            $altura_m = $altura / 100;
            $nuevoImc = round($nuevoPeso / ($altura_m * $altura_m), 1);
        }

        $medidasModel = new MedidasRegistro();
        $medidasModel->create($userId, $fechaHoy, $nuevoPeso);
        $userModel->updateBiometrics($userId, $nuevoPeso, $nuevoImc);

        header('Location: ' . url('/micuenta'));
        exit;
    }

    public function saveCheckin()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) { header('Location: ' . url('/login')); exit; }

        $userId   = $_SESSION['usuario_id'];
        $fechaHoy = date('Y-m-d');

        $data = [
            'agua_cumplido'          => isset($_POST['agua_cumplido']) ? 1 : 0,
            'sueno_cumplido'         => isset($_POST['sueno_cumplido']) ? 1 : 0,
            'entrenamiento_cumplido' => isset($_POST['entrenamiento_cumplido']) ? 1 : 0,
        ];

        $habitosCompletados = $data['agua_cumplido'] || $data['sueno_cumplido'] || $data['entrenamiento_cumplido'];

        $habitoModel   = new HabitoRegistro();
        $registroPrevio= $habitoModel->findByDate($userId, $fechaHoy);
        $habitoModel->saveCheckin($userId, $fechaHoy, $data);

        $userModel    = new User();
        $badgeService = new BadgeService();
        $desafioModel = new DesafioUsuario();

        $xpGanado        = 0;
        $insigniaGanada  = null;
        $insigniaDesafio = null;
        $subioDeNivel    = false;
        $nombreNuevoNivel= '';

        $esPrimerRegistroDelDia = !$registroPrevio || (
            $registroPrevio['agua_cumplido'] == 0 &&
            $registroPrevio['sueno_cumplido'] == 0 &&
            $registroPrevio['entrenamiento_cumplido'] == 0
        );

        if ($habitosCompletados && $esPrimerRegistroDelDia) {
            $xpPorCheckin = 10;
            $resultadoXp  = $userModel->addXp($userId, $xpPorCheckin);
            $xpGanado    += $xpPorCheckin;
            if ($resultadoXp['subio_de_nivel']) {
                $subioDeNivel     = true;
                $nombreNuevoNivel = $resultadoXp['nombre_nuevo_nivel'];
            }
            if (!$registroPrevio) {
                $insigniaGanada = $badgeService->otorgarInsigniaPorCodigo($userId, 'primer-checkin');
            }

            foreach (['agua','sueno','entrenamiento'] as $tipo) {
                if ($data[$tipo.'_cumplido']) {
                    $desafioModel->actualizarProgreso($userId, $tipo);
                    $progreso = $desafioModel->findProgresoByHabito($userId, $tipo);
                    if ($progreso && $progreso['completado'] == 1) {
                        $codigoInsignia   = 'reto-'.$tipo.'-'.$progreso['duracion_dias'];
                        $insDesafioGanada = $badgeService->otorgarInsigniaPorCodigo($userId, $codigoInsignia);
                        if ($insDesafioGanada) $insigniaDesafio = $insDesafioGanada;
                    }
                }
            }
        }

        $mensaje = "¡Check-in guardado!";
        if ($xpGanado > 0) $mensaje = "¡Check-in guardado! Ganaste +{$xpGanado} XP.";
        if ($subioDeNivel) $mensaje = "¡FELICIDADES! Subiste a: Nivel {$nombreNuevoNivel}. Ganaste +{$xpGanado} XP.";
        if ($insigniaGanada)  $mensaje .= " ¡Obtuviste la insignia '{$insigniaGanada}'!";
        if ($insigniaDesafio) $mensaje .= " ¡Completaste un desafío y ganaste la insignia '{$insigniaDesafio}'!";

        $_SESSION['flash_message'] = $mensaje;
        header('Location: ' . url('/progreso'));
        exit;
    }

    public function savePeso()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) { header('Location: ' . url('/login')); exit; }

        $userId    = $_SESSION['usuario_id'];
        $nuevoPeso = (float)($_POST['peso'] ?? 0);
        $fechaHoy  = date('Y-m-d');

        if ($nuevoPeso <= 0) { header('Location: ' . url('/progreso')); exit; }

        $userModel = new User();
        $usuario   = $userModel->findById($userId);
        $altura    = (float)($usuario['altura'] ?? 0);

        $nuevoImc = 0;
        if ($altura > 0) {
            $altura_m = $altura / 100;
            $nuevoImc = round($nuevoPeso / ($altura_m * $altura_m), 1);
        }

        $medidasModel = new MedidasRegistro();
        $medidasModel->saveOrUpdateByDate($userId, $fechaHoy, $nuevoPeso);
        $userModel->updateBiometrics($userId, $nuevoPeso, $nuevoImc);

        header('Location: ' . url('/progreso'));
        exit;
    }

    public function uploadPhoto()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) { header('Location: ' . url('/login')); exit; }

        $userId = $_SESSION['usuario_id'];
        $nota   = trim($_POST['nota'] ?? '');
        $fecha  = date('Y-m-d');

        if (isset($_FILES['foto_progreso']) && $_FILES['foto_progreso']['error'] === UPLOAD_ERR_OK) {
            $file       = $_FILES['foto_progreso'];
            $allowed    = ['image/jpeg','image/png'];
            $maxSize    = 5 * 1024 * 1024;

            if (in_array($file['type'], $allowed) && $file['size'] <= $maxSize) {
                $ext           = pathinfo($file['name'], PATHINFO_EXTENSION);
                $nombreArchivo = $userId . '_' . time() . '.' . $ext;
                $rutaDestino   = BASE_PATH . '/public/assets/uploads/progreso/' . $nombreArchivo;
                $dir           = dirname($rutaDestino);
                if (!is_dir($dir)) mkdir($dir, 0775, true);

                if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
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

    public function editCheckin()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) { header('Location: ' . url('/login')); exit; }

        $userId     = $_SESSION['usuario_id'];
        $fechaHoy   = date('Y-m-d');
        $habitoModel= new HabitoRegistro();
        $userModel  = new User();

        $registroHoy = $habitoModel->findByDate($userId, $fechaHoy);
        if (!$registroHoy) { header('Location: ' . url('/progreso')); exit; }

        $usuario = $userModel->findById($userId);

        view('home/progreso_checkin_editar', [
            'title'       => 'Editar Check-in',
            'usuario'     => $usuario,
            'registroHoy' => $registroHoy
        ]);
    }

    public function deletePhoto()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success'=>false,'message'=>'Acceso denegado.']); return;
        }

        $userId = $_SESSION['usuario_id'];
        $fotoId = (int)($_POST['foto_id'] ?? 0);
        if (!$fotoId) { echo json_encode(['success'=>false,'message'=>'ID inválido']); return; }

        $fotoModel = new ProgresoFoto();
        $foto      = $fotoModel->findByIdAndUser($fotoId, $userId);
        if (!$foto) { echo json_encode(['success'=>false,'message'=>'No encontrada']); return; }

        $nombreArchivo = $foto['nombre_archivo'];
        $rutaArchivo   = BASE_PATH . '/public/assets/uploads/progreso/' . $nombreArchivo;

        if ($fotoModel->deleteById($fotoId)) {
            if (file_exists($rutaArchivo)) @unlink($rutaArchivo);
            echo json_encode(['success'=>true,'message'=>'Foto eliminada.']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Error al eliminar.']);
        }
    }

    /* ==== Endpoints AJAX usados en el home ==== */

     private function requireUserId(): int {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $uid = (int)($_SESSION['usuario_id'] ?? 0);
        if (!$uid) {
            http_response_code(401);
            header('Content-Type: application/json');
            exit(json_encode(['ok'=>false,'error'=>'No autenticado']));
        }
        return $uid;
    }

    public function addAgua()
    {
        header('Content-Type: application/json');
        try {
            $uid   = $this->requireUserId();
            $delta = max(1, (int)($_POST['delta'] ?? 1));
            $P     = new Progreso();
            $res   = $P->addHoy($uid, 0, $delta);
            exit(json_encode(['ok'=>true] + $res));
        } catch (\Throwable $e) {
            http_response_code(500);
            exit(json_encode(['ok'=>false,'error'=>$e->getMessage()]));
        }
    }

    public function addMinutos()
    {
        header('Content-Type: application/json');
        try {
            $uid   = $this->requireUserId();
            $delta = max(1, (int)($_POST['delta'] ?? 5));
            $P     = new Progreso();
            $res   = $P->addHoy($uid, $delta, 0);
            exit(json_encode(['ok'=>true] + $res));
        } catch (\Throwable $e) {
            http_response_code(500);
            exit(json_encode(['ok'=>false,'error'=>$e->getMessage()]));
        }
    }

    public function stats()
    {
        header('Content-Type: application/json');
        try {
            $uid = $this->requireUserId();
            $P   = new Progreso();
            exit(json_encode([
                'ok'   => true,
                'hoy'  => $P->getHoy($uid),
                'racha'=> $P->getRacha($uid),
            ]));
        } catch (\Throwable $e) {
            http_response_code(500);
            exit(json_encode(['ok'=>false,'error'=>$e->getMessage()]));
        }
    }
}

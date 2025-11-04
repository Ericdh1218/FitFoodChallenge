<?php
namespace App\Controllers;

use App\Models\Ejercicio;
use App\Models\Actividad;
use App\Models\UsuarioEstado;
use App\Models\User;

class ActividadesController
{
    /** Página principal de Actividades */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login')); exit;
        }
        $userId = (int)$_SESSION['usuario_id'];

        $actividadModel = new Actividad();
        $estadoModel    = new UsuarioEstado();

        // Estado de hoy (cambios usados)
        $estadoHoy            = $estadoModel->getOrCreateHoy($userId);
        $usados               = (int)($estadoHoy['refrescos_neat_usados'] ?? 0);
        $refrescosRestantes   = max(0, 3 - $usados);

        // Actividad destacada
        $actividadDestacada = $actividadModel->getOneRandomActivity(); // si tu método acepta excluir, pásale null

        // Resto de NEAT excluyendo la destacada
        $idExcluir        = $actividadDestacada['id'] ?? null;
        $actividadesNeat  = $actividadModel->getNeatActivities($idExcluir);

        // Ejercicios express (ejemplo)
        $ejerciciosExpress = Ejercicio::findByFilter(['equipamiento' => 'Sin Equipo'], 3);

        // Conteo de completadas por usuario (lo usas en la vista)
        $conteos = $actividadModel->getCompletionCounts($userId);

        view('home/actividades', [
            'title'               => 'Actividades',
            'ejercicios'          => $ejerciciosExpress,
            'actividadDestacada'  => $actividadDestacada,
            'actividadesNeat'     => $actividadesNeat,
            'refrescosRestantes'  => $refrescosRestantes,
            'conteos'             => $conteos,
        ]);
    }

    /** AJAX: refresca la actividad NEAT del día */
    public function refrescarNeat()
{
    header('Content-Type: application/json');
    if (session_status() === PHP_SESSION_NONE) session_start();

    $respond = function(array $p, int $code = 200){
        http_response_code($code);
        exit(json_encode($p));
    };

    if (empty($_SESSION['usuario_id'])) {
        return $respond(['success'=>false,'message'=>'Acceso denegado.'], 401);
    }
    $userId = (int)$_SESSION['usuario_id'];

    $estadoModel = new UsuarioEstado();
    $estadoHoy   = $estadoModel->getOrCreateHoy($userId);
    $usados      = (int)($estadoHoy['refrescos_neat_usados'] ?? 0);

    if ($usados >= 3) {
        return $respond(['success'=>false,'message'=>'¡Ya usaste tus 3 cambios de hoy!']);
    }

    // Incrementa contador
    $estadoModel->usarRefresco($userId);
    $usados++;

    // ID actual (para evitar repetir)
    $excludeId      = (int)($_POST['exclude_id'] ?? 0);
    $actividadModel = new Actividad();

    // Tu modelo no acepta parámetros: pedimos una, y si coincide con la actual probamos del pool
    $nueva = $actividadModel->getOneRandomActivity(); // sin argumentos
    if (!$nueva || ($excludeId && (int)($nueva['id'] ?? 0) === $excludeId)) {
        $pool = $actividadModel->getNeatActivities($excludeId ?: null); // excluye la actual si tu método lo soporta
        if (!empty($pool)) {
            $nueva = $pool[array_rand($pool)];
        }
    }

    $restantes = max(0, 3 - $usados);
    return $respond([
        'success'            => true,
        'actividad'          => $nueva,
        'refrescosRestantes' => $restantes,
    ]);
}


    /** AJAX: completar NEAT (suma XP, actualiza conteo) */
    public function completarNeat()
    {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();

        $respond = function(array $p, int $code = 200){ http_response_code($code); exit(json_encode($p)); };

        if (empty($_SESSION['usuario_id'])) {
            return $respond(['success'=>false,'message'=>'Acceso denegado.'], 401);
        }
        $userId      = (int)$_SESSION['usuario_id'];
        $actividadId = (int)($_POST['actividad_id'] ?? 0);

        if ($actividadId <= 0) {
            return $respond(['success'=>false,'message'=>'ID de actividad no válido.']);
        }

        $actividadModel = new Actividad();
        $xpGanado       = (int)$actividadModel->completarActividad($userId, $actividadId);

        $conteos    = $actividadModel->getCompletionCounts($userId);
        $nuevoTotal = (int)($conteos[$actividadId] ?? 0);

        return $respond([
            'success'    => true,
            'message'    => '¡Actividad registrada! Ganaste ' . $xpGanado . ' XP.',
            'xp'         => $xpGanado,
            'nuevoTotal' => $nuevoTotal,
        ]);
    }
}

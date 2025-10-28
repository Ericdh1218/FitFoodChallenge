<?php
namespace App\Controllers;

use App\Models\Ejercicio; 
use App\Models\Actividad;
use App\Models\UsuarioEstado; // <-- AÑADIR
use App\Models\User;

class ActividadesController
{
    /**
     * Muestra el dashboard de Actividades (NEAT Aleatoria + Resto)
     */
    public function index(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login')); exit;
        }
        $userId = $_SESSION['usuario_id'];

        $actividadModel = new Actividad();
        $estadoModel = new UsuarioEstado();

        // 1. Obtener estado de hoy (refrescos usados)
        $estadoHoy = $estadoModel->getOrCreateHoy($userId);
        $refrescosRestantes = 3 - (int)$estadoHoy['refrescos_neat_usados'];

        // 2. Obtener Actividad Aleatoria del Día
        $actividadDestacada = $actividadModel->getOneRandomActivity();

        // 3. Obtener el resto de actividades (excluyendo la destacada)
        $idExcluir = $actividadDestacada ? $actividadDestacada['id'] : null;
        $actividadesNeat = $actividadModel->getNeatActivities($idExcluir);

        // 4. (Ejercicios Express se queda igual)
        $ejerciciosExpress = \App\Models\Ejercicio::findByFilter(
            ['equipamiento' => 'Sin Equipo'], 3
        );
        
        view('home/actividades', [
            'title' => 'Actividades',
            'ejercicios' => $ejerciciosExpress,
            'actividadDestacada' => $actividadDestacada, // Nueva
            'actividadesNeat' => $actividadesNeat,
            'refrescosRestantes' => $refrescosRestantes // Nueva
        ]);
    }

    /**
     * ==========================================
     * NUEVO MÉTODO (AJAX): Refresca la actividad NEAT del día
     * ==========================================
     */
    public function refrescarNeat()
    {
        header('Content-Type: application/json');
        $respondJson = fn($data) => exit(json_encode($data));
        
        if (!isset($_SESSION['usuario_id'])) {
            return $respondJson(['success' => false, 'message' => 'Acceso denegado.']);
        }
        $userId = $_SESSION['usuario_id'];

        $estadoModel = new UsuarioEstado();
        $estadoHoy = $estadoModel->getOrCreateHoy($userId);
        $refrescosUsados = (int)$estadoHoy['refrescos_neat_usados'];

        // Lógica de límite de 3 refrescos
        if ($refrescosUsados >= 3) {
            return $respondJson(['success' => false, 'message' => '¡Ya usaste tus 3 cambios de hoy!']);
        }

        // Incrementar el contador
        $estadoModel->usarRefresco($userId);
        
        // Obtener una nueva actividad
        $actividadModel = new Actividad();
        $nuevaActividad = $actividadModel->getOneRandomActivity();
        
        return $respondJson([
            'success' => true,
            'actividad' => $nuevaActividad,
            'refrescosRestantes' => 2 - $refrescosUsados // 3 (total) - ($refrescosUsados + 1)
        ]);
    }
    
    /**
     * ==========================================
     * NUEVO MÉTODO (AJAX): Completa una actividad NEAT
     * ==========================================
     */
    public function completarNeat()
    {
        header('Content-Type: application/json');
        $respondJson = fn($data) => exit(json_encode($data));
        
        if (!isset($_SESSION['usuario_id'])) {
            return $respondJson(['success' => false, 'message' => 'Acceso denegado.']);
        }
        $userId = $_SESSION['usuario_id'];
        $actividadId = (int)($_POST['actividad_id'] ?? 0);

        if (!$actividadId) {
            return $respondJson(['success' => false, 'message' => 'ID de actividad no válido.']);
        }

        $actividadModel = new Actividad();
        $xpGanado = $actividadModel->completarActividad($userId, $actividadId);

        // Devuelve el nuevo conteo total para esa actividad
        $conteos = $actividadModel->getCompletionCounts($userId);
        $nuevoTotal = $conteos[$actividadId] ?? 0;

        return $respondJson([
            'success' => true, 
            'message' => '¡Actividad registrada! Ganaste ' . $xpGanado . ' XP.',
            'xp' => $xpGanado,
            'nuevoTotal' => $nuevoTotal
        ]);
    }
}
<?php
namespace App\Controllers;

use App\Models\Glosario;
use App\Models\User;
use App\Services\BadgeService;

class GlosarioController
{
    public function index()
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . url('/login')); exit;
    }

    $model = new Glosario();

    $categoria = $_GET['categoria'] ?? '';

    if ($categoria) {
        $terminos = $model->getByCategory($categoria);
    } else {
        $terminos = $model->all();
    }

    // Agrupar términos por letra inicial
    $terminosAgrupados = [];
    foreach ($terminos as $t) {
        $inicial = strtoupper(mb_substr($t['termino'], 0, 1));
        $terminosAgrupados[$inicial][] = $t;
    }
    ksort($terminosAgrupados);

    view('home/glosario', [
        'title' => 'Glosario Fit',
        'terminosAgrupados' => $terminosAgrupados
    ]);
}

    public function showQuiz()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login')); exit;
        }

        $model = new Glosario();
        $preguntasQuiz = $model->getGlosarioQuizData(10); // 10 preguntas

        if (count($preguntasQuiz) < 4) {
            // No hay suficientes preguntas, redirige
            $_SESSION['flash_message'] = "No hay suficientes términos en el glosario para un quiz.";
            header('Location: ' . url('/glosario'));
            exit;
        }

        // Guardamos las respuestas correctas en la sesión para calificar
        $respuestasCorrectas = [];
        foreach ($preguntasQuiz as $p) {
            $respuestasCorrectas[$p['id_pregunta']] = $p['id_correcto'];
        }
        $_SESSION['quiz_respuestas'] = $respuestasCorrectas;
        
        view('home/glosario_quiz', [
            'title' => 'Quiz del Glosario',
            'preguntasJson' => json_encode($preguntasQuiz) // Pasa las preguntas como JSON
        ]);
    }

    /**
     * ==========================================
     * NUEVO MÉTODO: Procesa las respuestas del Quiz (AJAX)
     * ==========================================
     */
    public function submitQuiz()
    {
        header('Content-Type: application/json');
        $respondJson = fn($data) => exit(json_encode($data));
        
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['quiz_respuestas'])) {
            return $respondJson(['success' => false, 'message' => 'Sesión no válida o quiz no iniciado.']);
        }

        $userId = $_SESSION['usuario_id'];
        $respuestasUsuario = json_decode(file_get_contents('php://input'), true);
        $respuestasCorrectas = $_SESSION['quiz_respuestas'];
        unset($_SESSION['quiz_respuestas']); // Limpia la sesión

        $totalPreguntas = count($respuestasCorrectas);
        $aciertos = 0;

        // Calificar
        foreach ($respuestasUsuario as $resp) {
            $idPregunta = $resp['id_pregunta'];
            $idRespuesta = $resp['id_respuesta'];

            if (isset($respuestasCorrectas[$idPregunta]) && $respuestasCorrectas[$idPregunta] == $idRespuesta) {
                $aciertos++;
            }
        }

        // Calcular XP (ej. 5 XP por acierto)
        $xpGanado = $aciertos * 5;
        $mensaje = "Obtuviste $aciertos de $totalPreguntas correctas. ¡Ganaste $xpGanado XP!";

        // Otorgar XP
        if ($xpGanado > 0) {
            $userModel = new User();
            $resultadoXp = $userModel->addXp($userId, $xpGanado);
            
            if ($resultadoXp['subio_de_nivel']) {
                 $mensaje .= " ¡Además, subiste a: Nivel {$resultadoXp['nombre_nuevo_nivel']}!";
            }
        }

        // (Opcional: Otorgar insignia si saca 10/10)
        // if ($aciertos == $totalPreguntas) {
        //     $badgeService = new BadgeService();
        //     $badgeService->otorgarInsigniaPorCodigo($userId, 'quiz-perfecto');
        // }

        return $respondJson(['success' => true, 'message' => $mensaje, 'score' => "$aciertos / $totalPreguntas"]);
    }
}
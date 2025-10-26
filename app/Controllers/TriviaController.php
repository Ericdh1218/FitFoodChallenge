<?php
namespace App\Controllers;

use App\Models\Trivia;

class TriviaController
{
    /**
     * Endpoint AJAX para obtener una pregunta aleatoria.
     * Responde a: GET /trivia/pregunta-aleatoria?categoria=...
     */
    public function getRandomQuestionAjax()
    {
        header('Content-Type: application/json');

       $categoriasPermitidas = ['Nutrición', 'Frutas y Verduras'];

        $model = new Trivia();
        // === CAMBIO: Pasa el array de categorías ===
        $pregunta = $model->getRandomQuestion($categoriasPermitidas);

        if ($pregunta) {
            
            // === CORRECCIÓN AQUÍ ===
            // Mapeamos los datos de la BD al formato que espera el JavaScript
            $preguntaFormateada = [
                'id' => $pregunta['id'],
                'pregunta' => $pregunta['pregunta'],
                'opciones' => array_filter([ // Filtra opciones nulas
                    'a' => $pregunta['opcion_a'] ?? null,
                    'b' => $pregunta['opcion_b'] ?? null,
                    'c' => $pregunta['opcion_c'] ?? null,
                    'd' => $pregunta['opcion_d'] ?? null,
                ]),
                'correcta' => $pregunta['respuesta_correcta'], // <-- ¡EL CAMBIO CLAVE!
                'feedback_correcto' => $pregunta['feedback_correcto'],
                'feedback_incorrecto' => $pregunta['feedback_incorrecto']
            ];
            // ======================

            echo json_encode(['success' => true, 'pregunta' => $preguntaFormateada]);
        
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron más preguntas.']);
        }
        exit;
    }
}
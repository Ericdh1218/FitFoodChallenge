<?php
namespace App\Controllers;

use App\Models\Ejercicio;

class DeportesController
{
    public function index(): void
    {
        if (!empty($_GET['id'])) {
            $e = Ejercicio::find((int)$_GET['id']);
            if (!$e) { http_response_code(404); echo "Ejercicio no encontrado"; return; }
            view('home/deportes_detalle', ['e' => $e, 'title' => $e['nombre']]);
            return;
        }

        $f = [
            'q'                 => $_GET['q']                 ?? null,
            'grupo_muscular'    => $_GET['grupo_muscular']    ?? null,
            'tipo_entrenamiento'=> $_GET['tipo_entrenamiento']?? null,
            'equipamiento'      => $_GET['equipamiento']      ?? null,
        ];

        $ejercicios = Ejercicio::all($f);
        $grupos     = Ejercicio::grupos();
        $tipos      = Ejercicio::tipos();
        $equipos    = Ejercicio::equipos();

        view('home/deportes', [
            'title'      => 'Deportes',
            'ejercicios' => $ejercicios,
            'grupos'     => $grupos,
            'tipos'      => $tipos,
            'equipos'    => $equipos,
            'filtros'    => $f,
        ]);
    }
    /**
     * ==========================================
     * MÉTODO AJAX: Obtiene ejercicios aleatorios
     * ==========================================
     */
    public function getRandomEjerciciosAjax()
    {
        if (!isset($_SESSION['usuario_id'])) { exit; } // Protección simple

        $filters = [
            'equipamiento' => $_GET['equipamiento'] ?? 'Sin Equipo',
        ];
        $limit = (int)($_GET['limit'] ?? 3); // Cambiado a 3 como en tu JS

        $ejercicioModel = new Ejercicio();
        // Llama al método static findByFilter
        $ejercicios = Ejercicio::findByFilter($filters, $limit); 

        // Captura de Salida (Buffer)
        ob_start();
        
        // Carga la vista parcial con los nuevos ejercicios
        partial('home._partial_ejercicio_cards', ['ejercicios' => $ejercicios]);
        
        $html = ob_get_clean(); // Obtiene el HTML

        echo $html; // Devuelve el HTML
        exit;
    }
}

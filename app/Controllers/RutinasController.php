<?php
namespace App\Controllers;

use App\Models\Rutina;

class RutinasController
{
    /**
     * Muestra la lista de todas las rutinas prediseñadas.
     * Responde a la ruta: /rutinas
     */
    public function index()
    {
        // Solo usuarios logueados pueden ver esto
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $rutinaModel = new Rutina();
        $rutinas = $rutinaModel->getPredefinidas();

        view('home/rutinas', ['rutinas' => $rutinas]);
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
}
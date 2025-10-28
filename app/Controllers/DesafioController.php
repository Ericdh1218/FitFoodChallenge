<?php
namespace App\Controllers;

use App\Models\Desafio;
use App\Models\DesafioUsuario;

class DesafioController
{
    // Muestra la lista de todos los desafíos disponibles
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $model = new Desafio();
        $desafios = $model->getAll();

        view('home/desafios_lista', [
            'title' => 'Desafíos Disponibles',
            'desafios' => $desafios
        ]);
    }

    // Muestra la página de detalle de un desafío específico
    public function show()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        $userId = $_SESSION['usuario_id'];
        $codigo = $_GET['codigo'] ?? null;

        if (!$codigo) {
            header('Location: ' . url('/'));
            exit;
        }

        $model = new Desafio();
        $desafio = $model->findByCodigo($codigo);
        if (!$desafio) { /* Error 404 */
            exit('Desafío no encontrado');
        }

        // Revisa si el usuario ya se unió a este desafío
        $progresoModel = new DesafioUsuario();
        $progreso = $progresoModel->findProgreso($userId, $desafio['id']);
        $ejerciciosSugeridos = [];
        // Si es el reto 7x7, busca ejercicios "Sin Equipo"
        if ($desafio['codigo'] === 'reto-7x7') {
            // Asumimos que findByFilter es static. Si no, usa new Ejercicio()
            $ejerciciosSugeridos = \App\Models\Ejercicio::findByFilter(
                ['equipamiento' => 'Sin Equipo'],
                3 // Traer 3 ejemplos
            );
        }
        // (Podríamos añadir lógica similar para 'reto-agua' para mostrar artículos)
        // =====================================

        view('home/desafio_detalle', [
            'title' => $desafio['titulo'],
            'desafio' => $desafio,
            'progreso' => $progreso,
            'ejercicios' => $ejerciciosSugeridos // <-- Pasar ejercicios a la vista
        ]);
    }

    // Procesa la acción de "Unirse al Desafío"
    public function join()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        $userId = $_SESSION['usuario_id'];
        $desafioId = (int) ($_POST['desafio_id'] ?? 0);

        if (!$desafioId) { /* error */
        }

        $progresoModel = new DesafioUsuario();
        $progresoModel->unirse($userId, $desafioId);

        // Redirige de vuelta a la página del desafío
        $_SESSION['flash_message'] = '¡Te has unido al desafío!';

        // Necesitamos el 'codigo' para redirigir
        $desafioModel = new Desafio();
        // (Necesitaríamos un findById, o pasar el 'codigo' en el form)
        // Por ahora, redirigimos a la lista general
        header('Location: ' . url('/desafios'));
        exit;
    }
}
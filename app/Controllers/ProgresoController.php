<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProgresoService;

class ProgresoController extends Controller
{
    public function index(): void
    {
        $title = 'Mi progreso';
        $this->render('home/progreso', compact('title'));
    }

    public function store(): void
    {
        // demo: usuario 1 (cámbialo cuando tengas auth)
        $userId = $_SESSION['user_id'] ?? 1;
        $minutes = max(0, (int)($_POST['minutes'] ?? 0));
        $water   = max(0, (int)($_POST['water'] ?? 0));

        $svc = new ProgresoService();
        $ok  = $svc->saveToday($userId, $minutes, $water);

        header('Content-Type: application/json');
        echo json_encode(['ok' => $ok]);
    }
}

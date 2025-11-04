<?php
namespace App\Controllers;

use App\Models\Progreso;

class HomeController
{
    public function index()
    {
        $stats = ['racha' => 0, 'min_actividad' => 0, 'vasos_agua' => 0];

        if (!empty($_SESSION['usuario_id'])) {
            $uid = (int)$_SESSION['usuario_id'];
            $P   = new Progreso();
            $hoy = $P->getHoy($uid);

            $stats['racha']         = $P->getRacha($uid);
            $stats['min_actividad'] = (int)($hoy['min_actividad'] ?? 0);
            $stats['vasos_agua']    = (int)($hoy['vasos_agua'] ?? 0);
        }

        view('home/index', ['stats' => $stats]);
    }
}

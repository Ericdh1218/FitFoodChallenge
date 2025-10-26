<?php
namespace App\Controllers;

use App\Models\Glosario;

class GlosarioController
{
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login')); exit;
        }

        $model = new Glosario();
        $terminos = $model->all();

        // Agrupar términos por letra inicial (opcional, para mejor UI)
        $terminosAgrupados = [];
        foreach ($terminos as $t) {
            $inicial = strtoupper(mb_substr($t['termino'], 0, 1));
            if (!isset($terminosAgrupados[$inicial])) {
                $terminosAgrupados[$inicial] = [];
            }
            $terminosAgrupados[$inicial][] = $t;
        }
        ksort($terminosAgrupados); // Ordenar por letra

        view('home/glosario', [
            'title' => 'Glosario Fit',
            'terminosAgrupados' => $terminosAgrupados
        ]);
    }
}
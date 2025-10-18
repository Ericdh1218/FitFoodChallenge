<?php
namespace App\Controllers;

use App\Core\Controller;

class ActividadesController extends Controller
{
    public function index(): void
    {
        $title = 'Actividades físicas';
        $this->render('home/actividades', compact('title'));
    }
}

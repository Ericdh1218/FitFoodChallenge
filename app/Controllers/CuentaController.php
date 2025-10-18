<?php
namespace App\Controllers;

use App\Core\Controller;

class CuentaController extends Controller
{
    public function index(): void
    {
        $title = 'Mi cuenta';
        $this->render('home/miCuenta', compact('title'));
    }
}

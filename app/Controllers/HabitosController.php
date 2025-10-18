<?php
namespace App\Controllers;

use App\Core\Controller;

class HabitosController extends Controller
{
    public function index(): void
    {
        $title = 'Hábitos';
        $this->render('home/habitos', compact('title'));
    }
}

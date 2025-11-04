<?php
namespace App\Controllers\Admin;

use App\Core\AdminMiddleware;
use App\Models\Desafio;

class AdminDesafiosController
{
    public function __construct() { AdminMiddleware::check(); }

    public function index()
{
    $m = new Desafio();
    view('admin/desafios/index', [
        'title'    => 'Gestionar Desafíos',
        'desafios' => $m->getAll(),   // <- usa getAll()
    ], 'admin');
}

    public function create()
    {
        view('admin/desafios/create', [
            'title' => 'Nuevo Desafío',
        ], 'admin');
    }

    public function store()
    {
        $m = new Desafio();
        $m->create($_POST);
        header('Location: ' . url('/admin/desafios')); exit;
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $m  = new Desafio();
        $d  = $m->find($id);
        if (!$d) { echo "Desafío no encontrado"; return; }

        view('admin/desafios/edit', [
            'title' => 'Editar Desafío',
            'd'     => $d,
        ], 'admin');
    }

    public function update()
    {
        $id = (int)($_POST['id'] ?? 0);
        $m  = new Desafio();
        $m->update($id, $_POST);
        header('Location: ' . url('/admin/desafios')); exit;
    }

    public function destroy()
    {
        $id = (int)($_POST['id'] ?? 0);
        $m  = new Desafio();
        $m->delete($id);
        header('Location: ' . url('/admin/desafios')); exit;
    }
}

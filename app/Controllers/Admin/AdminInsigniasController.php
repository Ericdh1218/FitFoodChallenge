<?php
namespace App\Controllers\Admin;

use App\Core\AdminMiddleware;
use App\Models\Insignia;

class AdminInsigniasController
{
    public function __construct(){ AdminMiddleware::check(); }

    public function index(){
        $m = new Insignia();
        view('admin/insignias/index', [
            'title'     => 'Insignias',
            'insignias' => $m->all(),
        ], 'admin');
    }

    public function create(){
        view('admin/insignias/create', ['title'=>'Nueva Insignia'], 'admin');
    }

    public function store(){
        $m = new Insignia();
        $m->create($_POST, $_FILES['icono_url'] ?? null);
        header('Location: '.url('/admin/insignias')); exit;
    }

    public function edit(){
        $id = (int)($_GET['id'] ?? 0);
        $m  = new Insignia();
        $i  = $m->find($id);
        if (!$i){ echo "Insignia no encontrada"; return; }
        view('admin/insignias/edit', ['title'=>'Editar Insignia','i'=>$i], 'admin');
    }

    public function update(){
        $id = (int)($_POST['id'] ?? 0);
        $m  = new Insignia();
        $m->update($id, $_POST, $_FILES['icono_url'] ?? null);
        header('Location: '.url('/admin/insignias')); exit;
    }

    public function delete(){
        $id = (int)($_POST['id'] ?? 0);
        $m  = new Insignia();
        $m->delete($id);
        header('Location: '.url('/admin/insignias')); exit;
    }
}

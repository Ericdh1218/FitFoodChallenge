<?php
namespace App\Controllers\Admin;

use App\Core\AdminMiddleware; // Importa el guardián

class AdminDashboardController
{
    public function __construct()
    {
        // 1. ¡Proteger!
        AdminMiddleware::check();
    }

    /**
     * Muestra la página principal del admin.
     */
    public function index()
    {
        // 2. Llama a la vista usando el layout 'admin'
        view('admin/dashboard', [
            'title' => 'Dashboard'
        ], 'admin'); // <-- 3. Especifica el layout 'admin'
    }
}
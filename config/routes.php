<?php
use App\Controllers\HomeController;
use App\Controllers\ActividadesController;
use App\Controllers\HabitosController;
use App\Controllers\ProgresoController;
use App\Controllers\CuentaController;
use App\Controllers\DeportesController;
use App\Controllers\RegistroController;
use App\Controllers\LoginController;


$router->get('/', [HomeController::class,'index']);
$router->get('/actividades', [ActividadesController::class,'index']);
$router->get('/habitos', [HabitosController::class,'index']);
$router->get('/progreso', [ProgresoController::class,'index']);
$router->get('/deportes', [DeportesController::class,'index']);
$router->get('/registro', [RegistroController::class, 'show']);
$router->post('/registro', [RegistroController::class, 'store']);
$router->get('/login', [LoginController::class, 'show']);
$router->post('/login', [LoginController::class, 'store']);
// ==============================================

$router->get('/micuenta', [CuentaController::class,'index']); // <-- CORRECTA
$router->get('/logout', [LoginController::class, 'logout']);
$router->post('/progreso', [ProgresoController::class,'store']);
// ... tus otras rutas ...
$router->get('/habitos-form', [HabitosController::class, 'create']);

// === AÑADE ESTA LÍNEA ===
$router->post('/habitos-form', [HabitosController::class, 'store']);

$router->get('/test-db', function () {
    $pdo = \App\Config\DB::conn();
    echo 'OK: ' . $pdo->query('SELECT 1')->fetchColumn();
});

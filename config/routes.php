<?php
use App\Controllers\HomeController;
use App\Controllers\ActividadesController;
use App\Controllers\HabitosController;
use App\Controllers\ProgresoController;
use App\Controllers\CuentaController;
use App\Controllers\DeportesController;

$router->get('/', [HomeController::class,'index']);
$router->get('/actividades', [ActividadesController::class,'index']);
$router->get('/habitos', [HabitosController::class,'index']);
$router->get('/progreso', [ProgresoController::class,'index']);
$router->get('/deportes', [DeportesController::class,'index']);

$router->post('/micuenta', [CuentaController::class,'update']);


$router->post('/progreso', [ProgresoController::class,'store']);

$router->get('/test-db', function () {
    $pdo = \App\Config\DB::conn();
    echo 'OK: ' . $pdo->query('SELECT 1')->fetchColumn();
});

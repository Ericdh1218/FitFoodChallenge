<?php
use App\Controllers\HomeController;
use App\Controllers\ActividadesController;
use App\Controllers\HabitosController;
use App\Controllers\ProgresoController;
use App\Controllers\CuentaController;

$router->get('/', [HomeController::class,'index']);
$router->get('/actividades', [ActividadesController::class,'index']);
$router->get('/habitos', [HabitosController::class,'index']);
$router->get('/progreso', [ProgresoController::class,'index']);
$router->get('/micuenta', [CuentaController::class,'index']);

$router->post('/progreso', [ProgresoController::class,'store']);

<?php
use App\Controllers\HomeController;
use App\Controllers\ActividadesController;
use App\Controllers\HabitosController;
use App\Controllers\ProgresoController;
use App\Controllers\CuentaController;
use App\Controllers\DeportesController;
use App\Controllers\RegistroController;
use App\Controllers\LoginController;
use App\Controllers\RutinasController;
use App\Controllers\RecetasController;
use App\Controllers\GuiaNutricionalController;
use App\Controllers\ArticulosController;
use App\Controllers\GlosarioController;
use App\Controllers\AlimentosController;
use App\Controllers\PlanController;
use App\Controllers\TriviaController;

$router->get('/trivia/pregunta-aleatoria', [TriviaController::class, 'getRandomQuestionAjax']);


$router->get('/rutinas', [RutinasController::class, 'index']); // Lista prediseñadas
$router->get('/rutina', [RutinasController::class, 'show']);  // Detalle prediseñada

// === AÑADE ESTAS DOS LÍNEAS ===
$router->get('/rutinas/crear', [RutinasController::class, 'create']); // Muestra el form
$router->post('/rutinas/crear', [RutinasController::class, 'store']); // Guarda la nueva rutina
// =============================

$router->get('/', [HomeController::class,'index']);
$router->get('/actividades', [ActividadesController::class,'index']);

$router->get('/habitos', [HabitosController::class,'index']);
$router->get('/habitos/plan', [HabitosController::class, 'showPlan']);
$router->post('/habitos/advance-step', [HabitosController::class, 'advancePlanStep']);

$router->get('/progreso', [ProgresoController::class,'index']);

$router->get('/ejercicios/aleatorios', [DeportesController::class, 'getRandomEjerciciosAjax']);
$router->get('/deportes', [DeportesController::class,'index']);
$router->get('/rutinas', [RutinasController::class, 'index']);
$router->get('/rutina', [RutinasController::class, 'show']);
$router->get('/rutinas/crear', [RutinasController::class, 'create']); // Muestra el form
$router->post('/rutinas/crear', [RutinasController::class, 'store']); // Guarda la nueva rutina
$router->get('/rutina/editar', [RutinasController::class, 'edit']); // Muestra la pág. de edición
$router->post('/rutina/agregar-ejercicio', [RutinasController::class, 'addEjercicio']); // Procesa añadir ejercicio
$router->post('/rutina/eliminar', [RutinasController::class, 'delete']);
$router->post('/rutina/quitar-ejercicio', [RutinasController::class, 'removeEjercicio']);
$router->get('/mi-rutina', [RutinasController::class, 'showPersonal']);

$router->get('/registro', [RegistroController::class, 'show']);
$router->post('/registro', [RegistroController::class, 'store']);
$router->get('/login', [LoginController::class, 'show']);
$router->post('/login', [LoginController::class, 'store']);
$router->get('/micuenta', [CuentaController::class,'index']); // <-- CORRECTA
$router->get('/logout', [LoginController::class, 'logout']);
$router->get('/habitos-form', [HabitosController::class, 'create']);
$router->post('/habitos-form', [HabitosController::class, 'store']);

$router->post('/articulo/marcar-leido', [ArticulosController::class, 'markAsRead']);
$router->get('/articulos', [ArticulosController::class, 'index']);
$router->get('/articulo', [ArticulosController::class, 'show']);
$router->get('/glosario', [GlosarioController::class, 'index']);
$router->get('/glosario', [GlosarioController::class, 'index']);

$router->post('/progreso', [ProgresoController::class,'store']);
$router->post('/progreso/checkin', [ProgresoController::class, 'saveCheckin']);


$router->get('/recetas', [RecetasController::class, 'index']);
$router->get('/receta', [RecetasController::class, 'show']);
$router->get('/guia-nutricional', [GuiaNutricionalController::class, 'index']);
$router->get('/mis-recetas', [RecetasController::class, 'myRecipes']);
$router->post('/recetas/crear', [RecetasController::class, 'store']); // Guarda la receta
$router->get('/alimentos', [AlimentosController::class, 'index']); // Llama al método index
$router->get('/alimentos/buscar', [AlimentosController::class, 'buscar']); // Ruta AJAX (ya la tienes)
$router->get('/recetas/crear', [RecetasController::class, 'create']); // Muestra form para crear

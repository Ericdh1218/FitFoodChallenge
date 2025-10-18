<?php
declare(strict_types=1);
define('BASE_PATH', dirname(__DIR__));

// Debug duro por ahora
error_reporting(E_ALL);
ini_set('display_errors', '1');

require BASE_PATH . '/config/bootstrap.php';

use App\Core\Router;

$router = new Router();

// Rutas
require BASE_PATH . '/config/routes.php';

// Despacho
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

<?php
namespace App\Core;

class Router {
    private array $routes = ['GET'=>[], 'POST'=>[]];

    public function get(string $path, $handler){ $this->routes['GET'][$this->normalize($path)] = $handler; }
    public function post(string $path, $handler){ $this->routes['POST'][$this->normalize($path)] = $handler; }

    public function dispatch(string $method, string $uri): void {
        // Soporta bonitas y ?r=
        $path = isset($_GET['r']) && $_GET['r'] !== '' ? '/'.trim($_GET['r'],'/') : $this->normalize($this->stripBase($uri));

        $handler = $this->routes[$method][$path] ?? null;
        if (!$handler) {
            http_response_code(404);
            echo 'Ruta no encontrada.';
            return;
        }
        if (is_array($handler)) {
            [$class,$action] = $handler;
            $controller = new $class();
            $controller->$action();
        } else {
            call_user_func($handler);
        }
    }
    private function normalize(string $p): string { $p = '/'.trim($p,'/'); return $p === '/' ? '/' : rtrim($p,'/'); }
    private function stripBase(string $uri): string {
        $script = $_SERVER['SCRIPT_NAME'];                 // /fitfoodchallenge/public/index.php
        $base   = str_replace('/index.php','',$script);    // /fitfoodchallenge/public
        return preg_replace('#^'.preg_quote($base,'#').'#','',$uri) ?: '/';
    }
}

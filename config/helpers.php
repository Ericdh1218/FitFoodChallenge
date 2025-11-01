<?php
/**
 * Renderiza una vista con un layout.
 *
 * @param string $viewName El nombre de la vista (ej. 'home/index')
 * @param array $data Los datos a pasar a la vista
 * @param string $layout El nombre del layout (ej. 'main' o 'admin')
 */
function env(string $k, $d = null)
{
    return $_ENV[$k] ?? getenv($k) ?: $d;
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $v = $_ENV[$key] ?? getenv($key);
        return $v === false || $v === null ? $default : $v;
    }
}


function url(string $path = ''): string
{
    $base = rtrim(env('APP_URL', ''), '/');
    if ($base === '') {
        // $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') ? 'https' : 'http';
        //$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            // Detecta el protocolo (http o https) de un proxy como ngrok
            $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');

        // Detecta el dominio (host) de un proxy como ngrok
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'); // …/public
        $base = "{$scheme}://{$host}{$script}";
    }
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function view(string $viewName, array $data = [], string $layout = 'main')
{
    // Extrae los datos para que estén disponibles como variables
    extract($data);
    
    // Construye la ruta al archivo de la vista
    $viewFile = BASE_PATH . '/app/Views/' . str_replace('.', '/', $viewName) . '.php';
    
    if (file_exists($viewFile)) {
        // Carga el layout principal, el cual incluirá el $viewFile
        $layoutFile = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo "Error: Layout '{$layout}' no encontrado.";
        }
    } else {
        echo "Error: Vista '{$viewName}' no encontrada.";
    }
}
/**
 * Renderiza una vista "parcial" (solo el archivo) sin el layout principal.
 * Se usa para AJAX o para incluir vistas dentro de otras vistas.
 *
 * @param string $viewName El nombre de la vista (ej. 'home._partial_ejercicio_cards')
 * @param array $data Los datos a pasar a la vista
 */
function partial(string $viewName, array $data = [])
{
    // Extrae los datos para que estén disponibles como variables en la vista
    extract($data); 
    
    // Construye la ruta al archivo de la vista
    $viewFile = BASE_PATH . '/app/Views/' . str_replace('.', '/', $viewName) . '.php';
    
    if (file_exists($viewFile)) {
        // Incluye SÓLO el archivo de la vista, sin main.php
        include $viewFile; 
    } else {
        // Muestra un error si el archivo parcial no se encuentra
        echo "<p style='color:red;'>Error: Vista parcial no encontrada: {$viewFile}</p>";
    }
}
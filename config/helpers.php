<?php
function env(string $k, $d=null){ return $_ENV[$k] ?? getenv($k) ?: $d; }

function url(string $path=''): string {
    $base = rtrim(env('APP_URL',''),'/');
    if ($base==='') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'); // …/public
        $base = "{$scheme}://{$host}{$script}";
    }
    return rtrim($base,'/') . '/' . ltrim($path,'/');
}

function view(string $template, array $data=[]){
    extract($data);
    $viewFile = BASE_PATH . '/app/Views/' . $template . '.php';
    if (!file_exists($viewFile)) { http_response_code(404); echo 'Vista no encontrada'; return; }
    include BASE_PATH . '/app/Views/layouts/main.php';
}

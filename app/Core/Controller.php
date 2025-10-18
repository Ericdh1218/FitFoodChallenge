<?php
namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        // $view: e.g. 'home/index' (carpeta dentro de app/Views)
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo 'Vista no encontrada: ' . htmlspecialchars($view);
            return;
        }
        extract($data);
        include BASE_PATH . '/app/Views/layouts/main.php';
    }
}

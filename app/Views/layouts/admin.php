<?php
// (Cargamos las variables $title y $viewFile que pasa el helper view())
$pageTitle = $title ?? 'Admin Panel';
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FitFood Admin</title>
    <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>">
    <style>
        /* Estilos simples para el layout del Admin */
        body {
            display: flex;
            background: var(--bg);
        }
        .admin-sidebar {
            width: 240px;
            background: var(--panel);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            border-right: 1px solid var(--line);
        }
        .admin-sidebar h2 {
            font-family: Poppins, Inter, sans-serif;
            color: var(--brand);
            margin: 0 0 24px 0;
        }
        .admin-sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .admin-sidebar nav a {
            padding: 10px 14px;
            border-radius: 8px;
            color: var(--muted);
            font-weight: 500;
        }
        .admin-sidebar nav a:hover {
            background: var(--card);
            color: var(--text);
        }
        .admin-main {
            margin-left: 240px; /* Mismo ancho que el sidebar */
            padding: 32px;
            width: calc(100% - 240px);
        }
    </style>
</head>
<body>

    <aside class="admin-sidebar">
        <h2>FitFood Admin</h2>
        <nav>
            <a href="<?= url('/admin/dashboard') ?>">Dashboard</a>
            <a href="<?= url('/admin/users') ?>">Usuarios</a>
            <a href="<?= url('/admin/recetas') ?>">Recetas</a>
            <a href="<?= url('/admin/ejercicios') ?>">Ejercicios</a>
            <a href="<?= url('/admin/rutinas') ?>">Rutinas</a>
            <a href="<?= url('/admin/desafios') ?>">Desafíos</a>
            <hr style="border-color: var(--line);">
            <a href="<?= url('/') ?>" style="color: var(--brand-2);">&larr; Volver al Sitio</a>
        </nav>
    </aside>

    <main class="admin-main">
        <?php
        // Carga la vista específica (ej. dashboard.php, users/index.php)
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<p>Error: Vista '{$viewFile}' no encontrada.</p>";
        }
        ?>
    </main>
    
</body>
</html>
<?php /** @var string $title */ ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($title) ? htmlspecialchars($title) : 'FitFoodChallenge' ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>">
  <script type="module" src="<?= url('assets/js/main.js') ?>" defer></script>
</head>
<body>
  <header class="site-header container">
  <a href="<?= url('/') ?>" class="brand">FitFood<span>Challenge</span></a>

  <button class="menu-toggle" aria-expanded="false" aria-label="Abrir menú">
    <span></span><span></span><span></span>
  </button>

  <nav class="nav" data-collapsible>
    <a href="<?= url('/actividades') ?>">Actividades</a>
    <a href="<?= url('/habitos') ?>">Hábitos</a>
    <a href="<?= url('/progreso') ?>">Progreso</a>
    <a href="<?= url('/articulos') ?>" style="border-top: 1px solid var(--line);">Biblioteca</a>
    <div class="dropdown">
        <a href="#" class="dropdown-toggle">Actividad fisica</a>
        
        <div class="dropdown-menu">
            <a href="<?= url('/deportes') ?>">Ejercicios</a>
            <a href="<?= url('/rutinas') ?>">Rutinas</a>
        </div>
    </div>
    <div class="dropdown">
        <a href="#" class="dropdown-toggle">Nutrición</a>
        
        <div class="dropdown-menu">
            <a href="<?= url('/recetas') ?>">Recetas</a>
            <a href="<?= url('/guia-nutricional') ?>" style="border-top: 1px solid var(--line);">Guía Nutricional</a>
        </div>
    </div>
    <div class="dropdown">
        <a href="#" class="dropdown-toggle">
            <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Mi Perfil') ?>
        </a>
        <div class="dropdown-menu">
            <a href="<?= url('/micuenta') ?>">Mi cuenta</a>
            <a href="<?= url('/logout') ?>" style="color: var(--danger, #ef4444);">Cerrar Sesión</a>
        </div>
    </div>
</nav>
</header>


  <main class="container">
    <?php include $viewFile; ?>
  </main>

  <footer class="site-footer container">
    <small>&copy; <?= date('Y') ?> FitFoodChallenge. Hecho con ❤.</small>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="module" src="<?= url('assets/js/main.js') ?>" defer></script>
</body>
</html>

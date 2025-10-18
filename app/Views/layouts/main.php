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
    <nav class="nav">
      <a href="<?= url('/actividades') ?>">Actividades</a>
      <a href="<?= url('/habitos') ?>">Hábitos</a>
      <a href="<?= url('/progreso') ?>">Progreso</a>
      <a href="<?= url('/micuenta') ?>">Mi cuenta</a>
    </nav>
  </header>

  <main class="container">
    <?php include $viewFile; ?>
  </main>

  <footer class="site-footer container">
    <small>&copy; <?= date('Y') ?> FitFoodChallenge. Hecho con ❤.</small>
  </footer>
</body>
</html>

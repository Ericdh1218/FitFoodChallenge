<?php
// Variables que inyecta tu helper `view()`
$pageTitle = $title ?? 'Admin Panel';
// $viewFile debe apuntar a la vista a renderizar dentro del layout
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($pageTitle) ?> | FitFood Admin</title>

  <!-- CSS global de tu app -->
  <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>" />
  <!-- CSS específico para el panel admin (hazlo responsivo aquí) -->
  <link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>" />

  <!-- Shim mínimo por si aún no cargas admin.css (opcional, se puede borrar) -->
  <style>
    :root{ --admin-sidebar-w:240px; }
    .admin-wrap{ display:grid; grid-template-columns: var(--admin-sidebar-w) 1fr; min-height:100vh; }
    .admin-sidebar{ position:sticky; top:0; height:100dvh; overflow:auto; background:var(--panel); border-right:1px solid var(--line); padding:20px; }
    .admin-sidebar h2{ margin:0 0 20px; color:var(--brand); font-family:Poppins,Inter,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif; }
    .admin-sidebar nav{ display:flex; flex-direction:column; gap:8px; }
    .admin-sidebar nav a{ padding:10px 14px; border-radius:8px; color:var(--muted); font-weight:500; text-decoration:none; }
    .admin-sidebar nav a:hover{ background:var(--card); color:var(--text); }
    .admin-main{ min-width:0; padding:24px; }
    .admin-topbar{ display:none; align-items:center; gap:12px; margin-bottom:16px; }
    #sidebarToggle{ display:inline-flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:10px; border:1px solid var(--line); background:var(--card); cursor:pointer; }
    @media (max-width:1024px){
      .admin-wrap{ grid-template-columns:1fr; }
      .admin-sidebar{ position:fixed; left:-260px; width:var(--admin-sidebar-w); z-index:30; transition:left .25s ease; }
      .admin-sidebar.open{ left:0; }
      .admin-main{ padding:16px; }
      .admin-topbar{ display:flex; }
    }
  </style>
</head>
<body>

  <div class="admin-wrap">

    <!-- Sidebar -->
    <aside id="adminSidebar" class="admin-sidebar" aria-label="Navegación de administrador">
      <h2>FitFood Admin</h2>
      <nav>
        <a href="<?= url('/admin/dashboard') ?>">Dashboard</a>
        <a href="<?= url('/admin/users') ?>">Usuarios</a>
        <a href="<?= url('/admin/recetas') ?>">Recetas</a>
        <a href="<?= url('/admin/ejercicios') ?>">Ejercicios</a>
        <a href="<?= url('/admin/rutinas') ?>">Rutinas</a>
        <a href="<?= url('/admin/desafios') ?>">Desafíos</a>
        <a href="<?= url('/admin/insignias') ?>">Insignias</a>

        <hr style="border-color:var(--line);">
        <a href="<?= url('/') ?>" style="color:var(--brand-2);">← Volver al Sitio</a>
      </nav>
    </aside>

    <!-- Contenido -->
    <main class="admin-main" role="main">
      <!-- Topbar visible en móviles: botón hamburguesa + título -->
      <div class="admin-topbar">
        <button id="sidebarToggle" type="button" aria-label="Abrir/cerrar menú lateral">☰</button>
        <h1 style="margin:0; font-size:1.05rem;"><?= htmlspecialchars($pageTitle) ?></h1>
      </div>

      <?php
        if (isset($viewFile) && is_file($viewFile)) {
          include $viewFile;
        } else {
          echo "<p>Error: Vista no encontrada.</p>";
        }
      ?>
    </main>

  </div>

  <!-- Toggle del sidebar para móvil -->
  <script>
    (function () {
      const btn = document.getElementById('sidebarToggle');
      const sb  = document.getElementById('adminSidebar');
      if (!btn || !sb) return;

      btn.addEventListener('click', () => sb.classList.toggle('open'));

      // Cerrar al hacer click fuera (solo en móvil)
      document.addEventListener('click', (e) => {
        const isMobile = window.matchMedia('(max-width:1024px)').matches;
        if (!isMobile) return;
        if (!sb.contains(e.target) && !btn.contains(e.target)) {
          sb.classList.remove('open');
        }
      });
    })();
  </script>

</body>
</html>

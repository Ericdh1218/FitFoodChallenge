<?php
/** @var array $articulos */
/** @var array $categorias */
/** @var string|null $filtroActual */
/** @var array $articulosLeidosIds */
?>

<style>
  /* ====== Ajustes de esta vista ====== */
  .cards .card-articulo img {
    width: 100%;
    height: 100%;
    object-fit: cover;          /* rellena recortando, sin deformar */
    display: block;
  }
  .cards .thumb {
    width: 100%;
    height: 160px;              /* altura fija del “marco” */
    border-radius: 12px;
    overflow: hidden;
    background: #0f172a;        /* fallback */
    margin-bottom: 12px;
  }
  /* Badge “Leído” visible en temas oscuros/claro */
  .tag-leido {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 999px;
    background: #22c55e;        /* verde */
    color: #ffffff;             /* texto alto contraste */
    font-weight: 700;
    font-size: 0.9rem;
    text-shadow: 0 1px 0 rgba(0,0,0,.15);
  }
  .tag-leido::before {
    content: "✔";
    font-weight: 900;
    line-height: 1;
  }
  /* Botón “Marcar como leído” más claro */
  .btn-marcar-leido {
    padding: 6px 12px;
    border-radius: 999px;
    background: transparent;
    border: 1px solid #22c55e;
    color: #22c55e;
    cursor: pointer;
  }
  .btn-marcar-leido:hover {
    background: #22c55e;
    color: #0b1b31;
  }

  /* Tarjeta del artículo: una sola columna en esta vista */
  .card-articulo-content { display: grid; gap: 8px; }
</style>

<h1>Biblioteca de Conocimiento</h1>
<p class="muted">Aprende sobre nutrición, ejercicio y hábitos saludables.</p>
<div class="card" style="
    background: linear-gradient(90deg,#22c55e,#16a34a);
    color:#fff;
    padding:12px 16px;
    border-radius:12px;
    margin-top:12px;
    font-weight:500;
    display:flex;
    align-items:center;
    gap:8px;
">
    <span style="font-size:1.4rem;">🎯</span>
    <span>Cada artículo leído te otorga <b>+20 puntos de experiencia (XP)</b>. ¡Sigue aprendiendo y sube de nivel!</span>
</div>
<?php
// --- Mensaje Flash ---
$mensajeFlash = null;
if (isset($_SESSION['flash_message'])) {
    $mensajeFlash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
?>
<?php if ($mensajeFlash): ?>
  <div class="flash-success card" style="margin-top: 16px; background: #15803d; color: #f0fdf4;">
      <?= htmlspecialchars($mensajeFlash) ?>
  </div>
<?php endif; ?>

<div class="layout-sidebar-wrapper" style="margin-top: 24px;">
  <aside class="layout-sidebar">
      <h3>Categorías</h3>
      <div class="filter-buttons">
          <a href="<?= url('/articulos') ?>" class="btn <?= !$filtroActual ? 'primary' : 'ghost' ?>">
              Todos
          </a>
          <?php foreach ($categorias as $cat): ?>
              <a href="<?= url('/articulos?categoria=' . urlencode($cat)) ?>"
                 class="btn <?= $filtroActual === $cat ? 'primary' : 'ghost' ?>">
                  <?= htmlspecialchars(ucfirst($cat)) ?>
              </a>
          <?php endforeach; ?>
      </div>
  </aside>

  <main class="layout-content">
    <?php if (empty($articulos)): ?>
      <div class="card muted">No hay artículos en esta categoría.</div>
    <?php else: ?>
      <div class="grid cards" style="grid-template-columns: 1fr;">
        <?php foreach ($articulos as $articulo): ?>
          <?php
            $yaLeido = in_array($articulo['id'], $articulosLeidosIds);
            $detalleUrlInterna = url('/articulo?id=' . $articulo['id']);

            // Imagen
            $img = null;
            if (!empty($articulo['imagen_url'])) {
                $imgUrl = $articulo['imagen_url'];
                $img = preg_match('~^https?://~', $imgUrl) ? $imgUrl : url('assets/img/' . $imgUrl);
            }
          ?>

          <div class="card card-articulo">
            <?php if ($img): ?>
              <a href="<?= htmlspecialchars($detalleUrlInterna) ?>" class="thumb">
                <img src="<?= htmlspecialchars($img) ?>" alt="" loading="lazy">
              </a>
            <?php endif; ?>

            <div class="card-articulo-content">
              <span class="tag"><?= htmlspecialchars(ucfirst($articulo['categoria'] ?? 'General')) ?></span>

              <h3>
                <a href="<?= htmlspecialchars($detalleUrlInterna) ?>" class="link-internal">
                  <?= htmlspecialchars($articulo['titulo']) ?>
                </a>
              </h3>

              <?php if (!empty($articulo['resumen'])): ?>
                <p class="muted"><?= htmlspecialchars($articulo['resumen']) ?>...</p>
              <?php endif; ?>

              <div style="margin-top: 8px;">
                <?php if ($yaLeido): ?>
                  <span class="tag-leido">Leído</span>
                <?php else: ?>
                  <form action="<?= url('/articulo/marcar-leido') ?>" method="POST" style="display:inline-block;">
                    <input type="hidden" name="articulo_id" value="<?= $articulo['id'] ?>">
                    <input type="hidden" name="categoria_actual" value="<?= htmlspecialchars($filtroActual ?? '') ?>">
                    <button type="submit" class="btn-marcar-leido">Marcar como leído</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
</div>

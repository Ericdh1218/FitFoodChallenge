<?php
/** @var array $articulos */
/** @var array $categorias */
/** @var string|null $filtroActual */
/** @var array $articulosLeidosIds */
?>

<h1>Biblioteca de Conocimiento</h1>
<p class="muted">Aprende sobre nutrición, ejercicio y hábitos saludables.</p>

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
                    // --- Preparamos variables DENTRO del bucle ---
                    $yaLeido = in_array($articulo['id'], $articulosLeidosIds);
                    $detalleUrlInterna = url('/articulo?id=' . $articulo['id']);

                    $img = null; // Inicializamos img para este artículo
                    if (!empty($articulo['imagen_url'])) {
                        $imgUrl = $articulo['imagen_url'];
                        if (!preg_match('~^https?://~', $imgUrl)) {
                            $img = url('assets/img/' . $imgUrl);
                        } else {
                            $img = $imgUrl;
                        }
                    }
                    // --- Fin preparación variables ---
                    ?>

                    <div class="card card-articulo">

                        <?php if ($img): // Usamos la variable $img ya procesada ?>
                            <a href="<?= htmlspecialchars($detalleUrlInterna) ?>">
                                <img src="<?= htmlspecialchars($img) ?>" alt="">
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

                            <div style="margin-top: 12px;">
                                <?php if ($yaLeido): ?>
                                    <p class="tag tag-leido">✓ Leído</p>
                                <?php else: ?>
                                    <form action="<?= url('/articulo/marcar-leido') ?>" method="POST"
                                        style="display: inline-block;">
                                        <input type="hidden" name="articulo_id" value="<?= $articulo['id'] ?>">
                                        <input type="hidden" name="categoria_actual"
                                            value="<?= htmlspecialchars($filtroActual ?? '') ?>">

                                        <button type="submit" class="link btn-marcar-leido">
                                            Marcar como leído ✔️
                                            </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div> <?php endforeach; ?>

            </div> <?php endif; ?>
    </main>
</div> ```
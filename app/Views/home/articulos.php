<?php
/** @var array $articulos */
/** @var array $categorias */
/** @var string|null $filtroActual */
?>

<h1>Biblioteca de Conocimiento</h1>
<p class="muted">Aprende sobre nutrición, ejercicio y hábitos saludables.</p>

<div class="layout-sidebar-wrapper" style="margin-top: 24px;">

    <aside class="layout-sidebar">
        <h3>Categorías</h3>
        <div class="filter-buttons">
            <a href="<?= url('/articulos') ?>" 
               class="btn <?= !$filtroActual ? 'primary' : 'ghost' ?>">
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
            <div class="grid cards" style="grid-template-columns: 1fr;"> <?php foreach ($articulos as $articulo): ?>
                    <a href="<?= url('/articulo?id=' . $articulo['id']) ?>" class="card card-articulo">
                        <?php if(!empty($articulo['imagen_url'])): 
                            $img = $articulo['imagen_url'];
                            // Asume que las imágenes de artículos SÍ son URLs completas o nombres de archivo en assets/img
                            if (!preg_match('~^https?://~', $img)) { $img = url('assets/img/' . $img); }
                        ?>
                        <img src="<?= htmlspecialchars($img) ?>" alt="">
                        <?php endif; ?>
                        <div>
                            <span class="tag"><?= htmlspecialchars(ucfirst($articulo['categoria'] ?? 'General')) ?></span>
                            <h3><?= htmlspecialchars($articulo['titulo']) ?></h3>
                            <p class="muted"><?= htmlspecialchars($articulo['extracto']) ?>...</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

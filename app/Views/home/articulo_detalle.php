<?php
/** @var array $articulo */
/** @var bool $yaLeido */
/** @var string|null $mensajeFlash */
?>
<article class="articulo-detalle-layout">

    <a class="link" href="<?= url('/articulos') ?>">← Volver a la Biblioteca</a>
    <?php if ($mensajeFlash): ?>
        <div class="flash-success card" style="margin-top: 16px; background: #15803d; color: #f0fdf4;">
            <?= htmlspecialchars($mensajeFlash) ?>
        </div>
    <?php endif; ?>

    <h1 style="margin-top: 16px;"><?= htmlspecialchars($articulo['titulo']) ?></h1>
    <p class="muted">
        Categoría: <?= htmlspecialchars(ucfirst($articulo['categoria'] ?? 'General')) ?> |
        Publicado: <?= isset($articulo['fecha_publicacion']) ? date('d/m/Y', strtotime($articulo['fecha_publicacion'])) : 'N/A' ?>
    </p>

    <div class="articulo-header-layout">

        <?php
        $img = null;
        if(!empty($articulo['imagen_url'])) {
            $imgUrl = $articulo['imagen_url'];
            if (!preg_match('~^https?://~', $imgUrl)) {
                $img = url('assets/img/' . $imgUrl);
            } else {
                $img = $imgUrl;
            }
        }

        if($img):
        ?>
        <div class="articulo-imagen-col">
            <img src="<?= htmlspecialchars($img) ?>" alt="" class="articulo-imagen-pequena">
            </div>
        <?php else: ?>
            <div></div>
        <?php endif; ?>
        <div class="articulo-resumen-col articulo-contenido section">
             <h3>Resumen</h3>
             <p><?= nl2br(htmlspecialchars($articulo['resumen'] ?? 'No hay resumen disponible.')) ?></p>
        </div>
        </div> <div style="margin-top: 24px;">
        <a href="<?= htmlspecialchars($articulo['url_externa']) ?>"
           target="_blank"
           rel="noopener noreferrer"
           class="btn primary">
            Leer Artículo Completo 🔗
        </a>
    </div>

    <div style="margin-top: 24px;">
        <?php if ($yaLeido): ?>
            <p class="tag tag-leido">✓ Leído</p>
        <?php else: ?>
            <form action="<?= url('/articulo/marcar-leido') ?>" method="POST">
                <input type="hidden" name="articulo_id" value="<?= $articulo['id'] ?>">
                <button type="submit" class="btn ghost">
                    Marcar como leído
                </button>
            </form>
        <?php endif; ?>
    </div>

</article>
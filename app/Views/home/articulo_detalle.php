<?php /** @var array $articulo */ ?>
<article>
    <a class="link" href="<?= url('/articulos') ?>">← Volver a la Biblioteca</a>
    
    <h1 style="margin-top: 16px;"><?= htmlspecialchars($articulo['titulo']) ?></h1>
    <p class="muted">
        Categoría: <?= htmlspecialchars(ucfirst($articulo['categoria'] ?? 'General')) ?> | 
        Publicado: <?= date('d/m/Y', strtotime($articulo['fecha_publicacion'])) ?>
    </p>

    <?php if(!empty($articulo['imagen_url'])): 
        $img = $articulo['imagen_url'];
        if (!preg_match('~^https?://~', $img)) { $img = url('assets/img/' . $img); }
    ?>
    <div style="border-radius:12px;overflow:hidden;margin:24px 0; aspect-ratio: 16/9; background: #333;">
        <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
    </div>
    <?php endif; ?>

    <div class="articulo-contenido section">
        <?= 
          // Usamos nl2br para convertir saltos de línea en <br>, 
          // pero sería mejor guardar el contenido en HTML o Markdown.
          nl2br(htmlspecialchars($articulo['contenido'])) 
        ?>
    </div>
    
    </article>

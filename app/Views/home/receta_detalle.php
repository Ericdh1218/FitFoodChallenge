<?php /** @var array $receta */ ?>
<article>
    <a class="link" href="<?= url('/recetas') ?>">← Volver al recetario</a>
    <h1 style="margin-top:8px"><?= htmlspecialchars($receta['titulo']) ?></h1>
    <span class="tag" style="margin-top: 8px;"><?= htmlspecialchars(ucfirst($receta['categoria'] ?? 'General')) ?></span>

    <?php
    $img = $receta['imagen'];
    if ($img && !preg_match('~^https?://~', $img)) {
        $img = url('assets/img/' . $img);
    }
    ?>
    <?php if (!empty($img)): ?>
        <div style="border-radius:12px;overflow:hidden;margin:16px 0; aspect-ratio: 16/9; background: #333;">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($receta['titulo']) ?>" 
                 style="width:100%;height:100%;object-fit:cover;">
        </div>
    <?php endif; ?>

    <div class="card" style="margin-top: 16px;">
        <h3>Descripción</h3>
        <p><?= nl2br(htmlspecialchars($receta['descripcion'] ?? '')) ?></p>
    </div>

    <div class="card" style="margin-top: 16px;">
        <h3>Ingredientes</h3>
        <ul style="padding-left: 20px; line-height: 1.7;">
            <?php foreach (explode("\n", $receta['ingredientes']) as $ingrediente): ?>
                <?php if(trim($ingrediente)): ?>
                    <li><?= htmlspecialchars(trim($ingrediente)) ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="card" style="margin-top: 16px;">
        <h3>Instrucciones</h3>
        <ol style="padding-left: 20px; line-height: 1.7;">
            <?php foreach (explode("\n", $receta['instrucciones']) as $paso): ?>
                 <?php if(trim($paso)): ?>
                    <li><?= htmlspecialchars(trim($paso)) ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </div>
</article>
<?php
/** @var array $ejercicios */
/** @var array $grupos */
/** @var array $tipos */
/** @var array $equipos */
/** @var array $filtros */
?>
<section>
    <h1 class="mb-2">Ejercicios</h1>

    <form method="get" action="<?= url('/deportes') ?>" class="grid"
        style="gap:12px;grid-template-columns:2fr 1fr 1fr 1fr auto">
        <input type="text" name="q" placeholder="Buscar por nombre o descripción"
            value="<?= htmlspecialchars((string) ($filtros['q'] ?? '')) ?>">

        <select name="grupo_muscular">
            <option value="">Grupo muscular</option>
            <?php foreach ($grupos as $g): ?>
                <option value="<?= htmlspecialchars($g) ?>" <?= ($filtros['grupo_muscular'] ?? '') === $g ? 'selected' : '' ?>>
                    <?= htmlspecialchars($g) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="tipo_entrenamiento">
            <option value="">Tipo</option>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>" <?= ($filtros['tipo_entrenamiento'] ?? '') === $t ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="equipamiento">
            <option value="">Equipo</option>
            <?php foreach ($equipos as $e): ?>
                <option value="<?= htmlspecialchars($e) ?>" <?= ($filtros['equipamiento'] ?? '') === $e ? 'selected' : '' ?>>
                    <?= htmlspecialchars($e) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn primary">Filtrar</button>
    </form>

    <?php if (!$ejercicios): ?>
        <div class="card" style="margin-top:16px;">No se encontraron ejercicios.</div>
    <?php else: ?>
        <div class="grid cards" style="margin-top:16px">
            <?php foreach ($ejercicios as $e): ?>
                <article class="card">
                    <header style="display:flex;justify-content:space-between;align-items:center;">
                        <h3 style="margin:0"><?= htmlspecialchars($e['nombre']) ?></h3>
                        <span class="badge"><?= htmlspecialchars($e['tipo_entrenamiento'] ?? '—') ?></span>
                    </header>
                    <p class="muted" style="margin:.5rem 0">
                        <?= htmlspecialchars($e['grupo_muscular'] ?: 'General') ?> •
                        <?= htmlspecialchars($e['equipamiento'] ?: 'Sin equipo') ?>
                    </p>

                    <?php
                    // Si en BD guardas sólo el nombre de archivo, búscalo en /assets/img/
                    $img = $e['media_url'];
                    if ($img && !preg_match('~^https?://~', $img)) {
                        $img = url('assets/img/' . $img);
                    }
                    $detalleUrl = url('/deportes?id=' . $e['id']);
                    ?>

                    <?php if (!empty($img)): ?>
                        <a href="<?= htmlspecialchars($detalleUrl) ?>" class="card-image-link">
                            <div style="border-radius:12px;overflow:hidden;margin:16px 0;">
                                <img src="<?= htmlspecialchars($img) ?>" alt="Ver detalle de <?= htmlspecialchars($e['nombre']) ?>"
                                    style="width:100%;height:auto;">
                            </div>
                        </a>
                    <?php endif; ?>

                    <a href="<?= htmlspecialchars($detalleUrl) ?>" class="link">Ver detalle →</a>

                    <p><?= htmlspecialchars(mb_strimwidth($e['descripcion'] ?? '', 0, 180, '…')) ?></p>

                    <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
                        <a class="link" href="<?= url('/deportes') . '?id=' . (int) $e['id'] ?>">Ver detalle →</a>
                        <?php if (!empty($e['video_url'])): ?>
                            <p><a class="link" target="_blank" rel="noopener" href="<?= htmlspecialchars($e['video_url']) ?>">Ver
                                    video 🎬</a></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
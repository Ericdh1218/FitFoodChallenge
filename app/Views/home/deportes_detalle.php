<?php /** @var array $e */ ?>
<article>
  <a class="link" href="<?= url('/deportes') ?>">← Volver</a>
  <h1 style="margin-top:8px"><?= htmlspecialchars($e['nombre']) ?></h1>
  <p class="muted">
    <?= htmlspecialchars($e['grupo_muscular'] ?? 'General') ?> •
    <?= htmlspecialchars($e['tipo_entrenamiento'] ?? '—') ?> •
    <?= htmlspecialchars($e['equipamiento'] ?? 'Sin equipo') ?>
  </p>

  <?php
    $img = $e['media_url'];
    if ($img && !preg_match('~^https?://~', $img)) {
        $img = url('assets/img/' . $img);
    }
  ?>
  <?php if (!empty($img)): ?>
    <div style="border-radius:12px;overflow:hidden;margin:16px 0;">
      <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:100%;height:auto;">
    </div>
  <?php endif; ?>

  <div class="card">
    <h3>Descripción</h3>
    <p><?= nl2br(htmlspecialchars($e['descripcion'] ?? '')) ?></p>
    <?php if (!empty($e['video_url'])): ?>
      <p><a class="link" target="_blank" rel="noopener" href="<?= htmlspecialchars($e['video_url']) ?>">Ver video 🎬</a></p>
    <?php endif; ?>
  </div>
</article>

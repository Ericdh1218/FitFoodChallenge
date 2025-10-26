<?php
/** @var array $ejercicios */
?>

<?php if (empty($ejercicios)): ?>
    <p class="muted" style="grid-column: 1 / -1; text-align: center;">No se encontraron más ejercicios "Sin Equipo".</p>
<?php endif; ?>

<?php foreach ($ejercicios as $ej): ?>
    <?php
    $imgSrc = null;
    if (!empty($ej['media_url'])) {
        $img = $ej['media_url'];
        if (!preg_match('~^https?://~', $img)) { $imgSrc = url('assets/img/'. $img); } else { $imgSrc = $img; }
    }
    ?>
    <a href="<?= url('/deportes?id=' . $ej['id']) ?>" class="guia-card">
        <?php if ($imgSrc): ?>
            <div style="aspect-ratio: 16/10; background: #333; overflow: hidden; border-radius: 4px;">
                <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($ej['nombre']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        <?php else: ?>
             <div style="aspect-ratio: 16/10; background: var(--card); border-radius: 4px;"></div>
        <?php endif; ?>
        <div class="guia-card-content">
            <h4><?= htmlspecialchars($ej['nombre']) ?></h4>
            <small class="muted"><?= htmlspecialchars($ej['grupo_muscular'] ?? '') ?></small>
        </div>
    </a>
<?php endforeach; ?>
<?php
/** @var array $rutina */
/** @var array $ejercicios */
?>
<article>
    <a class="link" href="<?= url('/rutinas') ?>">← Volver a Rutinas</a>

    <h1 style="margin-top:8px"><?= htmlspecialchars($rutina['nombre_rutina']) ?></h1>
    <p class="muted"><?= htmlspecialchars($rutina['descripcion']) ?></p>
    <span class="tag">Nivel: <?= htmlspecialchars($rutina['nivel']) ?></span>

    <h2 style="margin-top: 32px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">
        Ejercicios de la Rutina
    </h2>

    <div class="ejercicio-lista">
        <?php foreach ($ejercicios as $ejercicio): ?>
            <div class="ejercicio-item card">
                <div>
                    <h4><?= htmlspecialchars($ejercicio['nombre']) ?></h4>
                    <p class="muted">
                        <strong>Series/Reps:</strong> <?= htmlspecialchars($ejercicio['series_reps']) ?>
                    </p>
                </div>
                <?php
                    // 1. Codifica la URL actual de la rutina prediseñada para pasarla
                    $returnUrlRutina = urlencode(url('/rutina?id=' . $rutina['id'])); 
                ?>
                <a class="btn ghost" href="<?= url('/deportes?id=' . $ejercicio['id'] . '&return_url=' . $returnUrlRutina) ?>"> 
                    Ver Ejercicio
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</article>
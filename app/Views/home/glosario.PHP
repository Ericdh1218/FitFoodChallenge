<?php /** @var array $terminosAgrupados */ ?>

<h1>Glosario Fit</h1>
<p class="muted">Conceptos clave de nutrición y ejercicio explicados fácil.</p>

<div class="glosario-container" style="margin-top: 24px;">
    <?php foreach ($terminosAgrupados as $letra => $terminos): ?>
        <section class="glosario-letra-section">
            <h2><?= htmlspecialchars($letra) ?></h2>
            <?php foreach ($terminos as $t): ?>
                <div class="glosario-termino card">
                    <h3><?= htmlspecialchars($t['termino']) ?></h3>
                    <p class="muted"><?= nl2br(htmlspecialchars($t['definicion'])) ?></p>
                    <?php if(!empty($t['categoria'])): ?>
                        <span class="tag" style="margin-top: 8px;"><?= htmlspecialchars($t['categoria']) ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
</div>

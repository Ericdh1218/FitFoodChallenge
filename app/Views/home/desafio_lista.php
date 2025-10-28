<h1>Desafíos Disponibles</h1>
<p class="muted">Elige un reto y empieza a construir hábitos.</p>
<div class="grid cards">
    <?php foreach ($desafios as $desafio): ?>
    <a href="<?= url('/desafios/ver?codigo=' . $desafio['codigo']) ?>" class="card">
        <h3><?= htmlspecialchars($desafio['titulo']) ?></h3>
        <p class="muted"><?= htmlspecialchars($desafio['descripcion']) ?></p>
        <span class="link">Ver desafío (<?= $desafio['duracion_dias'] ?> días) &rarr;</span>
    </a>
    <?php endforeach; ?>
</div>
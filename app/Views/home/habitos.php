<?php /** @var array $areas */ ?>

<h1>Elige tu Próximo Hábito</h1>
<p class="muted">Selecciona un área en la que quieras enfocarte para mejorar.</p>

<div class="grid cards areas-grid" style="margin-top: 32px;">
    <?php foreach ($areas as $area): ?>
        <a href="<?= url('/habitos/plan?area=' . $area['codigo']) // Ruta que crearemos después ?>" class="card area-card">
          <div class="area-icono"><?= htmlspecialchars($area['icono'] ?? '') ?></div>
<h3><?= htmlspecialchars($area['titulo']) ?></h3>
            <p class="muted"><?= htmlspecialchars($area['descripcion_corta']) ?></p>
            <span class="link" style="margin-top: 10px;">Comenzar →</span>
        </a>
    <?php endforeach; ?>
</div>

<style>
.areas-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* 2 o más columnas */
}
.area-card {
    display: block; /* Para que el <a> funcione como tarjeta */
    text-align: center;
    padding: 24px;
}
.area-card:hover { /* Hereda el hover de .card */
    border-color: var(--brand-2);
}
.area-icono {
    font-size: 2.5rem; /* Icono grande */
    margin-bottom: 12px;
    line-height: 1;
}
.area-card h3 {
    margin-bottom: 8px;
}
.area-card p {
    font-size: 0.95rem;
}
</style>
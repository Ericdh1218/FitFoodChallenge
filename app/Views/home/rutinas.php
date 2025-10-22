<?php /** @var array $rutinas */ ?>

<h1 style="margin-bottom: 24px;">Rutinas Prediseñadas</h1>

<div class="grid cards" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
    <?php foreach ($rutinas as $rutina): ?>
        <div class="card">
            <span class="tag" style="margin-bottom: 8px;"><?= htmlspecialchars($rutina['nivel']) ?></span>
            <h3><?= htmlspecialchars($rutina['nombre_rutina']) ?></h3>
            <p><?= htmlspecialchars($rutina['descripcion']) ?></p>
            
            <a class="link" href="<?= url('/rutina?id=' . $rutina['id']) ?>" style="margin-top: 16px;">
                Ver Rutina Completa
            </a>
        </div>
    <?php endforeach; ?>
</div>
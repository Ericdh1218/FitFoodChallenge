<?php
/** @var array $rutinasPersonales */
/** @var array $rutinasPredefinidas */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1>Mis Rutinas</h1>
    <a href="<?= url('/rutinas/crear') ?>" class="btn primary">
        + Crear Nueva Rutina
    </a>
</div>

<?php if (empty($rutinasPersonales)): ?>
    <div class="card muted" style="margin-bottom: 32px;">
        Aún no has creado ninguna rutina personal. ¡Haz clic en "+ Crear Nueva Rutina"!
    </div>
<?php else: ?>
    <div class="grid cards" style="margin-bottom: 32px; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
        <?php foreach ($rutinasPersonales as $rutinaP): ?>
            <div class="card">
                <h3><?= htmlspecialchars($rutinaP['nombre_rutina']) ?></h3>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                    <a class="link" href="<?= url('/mi-rutina?id=' . $rutinaP['id']) ?>">
                    Ver Rutina
                </a>

                    <form action="<?= url('/rutina/eliminar') ?>" method="POST" class="delete-form" style="margin: 0;">
                        <input type="hidden" name="rutina_id" value="<?= $rutinaP['id'] ?>">
                        <button type="submit" class="btn ghost btn-delete" style="padding: 6px 10px; font-size: 0.9rem;">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<hr style="border: none; border-top: 1px solid var(--line); margin: 40px 0;">

<h2 style="margin-bottom: 24px;">Rutinas Prediseñadas</h2>

<?php if (empty($rutinasPredefinidas)): ?>
    <div class="card muted">No hay rutinas prediseñadas disponibles.</div>
<?php else: ?>
    <div class="grid cards" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
        <?php foreach ($rutinasPredefinidas as $rutinaPre): ?>
            <div class="card">
                <span class="tag" style="margin-bottom: 8px;"><?= htmlspecialchars($rutinaPre['nivel']) ?></span>
                <h3><?= htmlspecialchars($rutinaPre['nombre_rutina']) ?></h3>
                <p><?= htmlspecialchars($rutinaPre['descripcion']) ?></p>
                <a class="link" href="<?= url('/rutina?id=' . $rutinaPre['id']) ?>" style="margin-top: 16px;">
                    Ver Rutina Completa
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
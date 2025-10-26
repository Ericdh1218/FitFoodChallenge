<?php
/** @var array $usuario */
/** @var array|null $registroHoy */
/** @var array $historialHabitos */
/** @var array $historialPeso */

// Variable para saber si el historial está vacío
$historialVacio = empty($historialHabitos) && empty($historialPeso);
?>

<h1 style="margin-bottom: 8px;">Mi Progreso</h1>
<p class="muted">Registra tus hábitos diarios y observa tu evolución.</p>

<div class="auth-card-wrapper" style="padding-top: 16px;">
    <div class="auth-card" style="background: var(--panel); border: 1px solid var(--line);">

        <h2 class="auth-title" style="color: var(--text); font-size: 24px;">Check-in de Hoy (<?= date('d/m/Y') ?>)</h2>

        <?php if ($registroHoy): // Si YA registró hoy ?>
            <p class="checkin-completo">
                ¡Ya registraste tus hábitos de hoy! 👍
            </p>
            <div class="habitos-registrados">
                <?php if($registroHoy['agua_cumplido']) echo '<span class="tag tag-verde">💧 Agua</span>'; ?>
                <?php if($registroHoy['sueno_cumplido']) echo '<span class="tag tag-verde">😴 Sueño</span>'; ?>
                <?php if($registroHoy['entrenamiento_cumplido']) echo '<span class="tag tag-verde">🏋️ Entrenamiento</span>'; ?>
            </div>
            <p style="text-align:center; margin-top: 16px;">
                 <a href="<?= url('/progreso/checkin/editar') // Ruta futura para editar ?>" class="link muted">(Editar registro de hoy)</a>
            </p>

        <?php else: // Si NO ha registrado hoy, muestra el formulario ?>
            <p class="auth-subtitle" style="color: var(--muted);">
                ¿Cómo te fue hoy, <?= htmlspecialchars($usuario['nombre']) ?>?
            </p>
            <form action="<?= url('/progreso/checkin') ?>" method="POST" style="margin-top: 24px;">
                <div class="form-check-group">
                    <input type="checkbox" id="agua" name="agua_cumplido" value="1">
                    <label for="agua">
                        <strong>Agua:</strong>
                        <span>Completaste tu meta de <?= (int)($usuario['consumo_agua'] ?? 0) ?> vasos.</span>
                    </label>
                </div>
                <div class="form-check-group">
                    <input type="checkbox" id="sueno" name="sueno_cumplido" value="1">
                    <label for="sueno">
                        <strong>Sueño:</strong>
                        <span>Dormiste tus <?= (int)($usuario['horas_sueno'] ?? 0) ?> horas.</span>
                    </label>
                </div>
                <div class="form-check-group">
                    <input type="checkbox" id="entrenamiento" name="entrenamiento_cumplido" value="1">
                    <label for="entrenamiento">
                        <strong>Entrenamiento:</strong>
                        <span>Realizaste tu rutina de ejercicio de hoy.</span>
                    </label>
                </div>
                <button type="submit" class="btn primary" style="width:100%; margin-top: 24px;">
                    Guardar mi Progreso ✅
                </button>
            </form>
        <?php endif; ?>

   <?php
// Variable para saber si el historial está vacío
$historialVacio = empty($historialHabitos) && empty($historialPeso);
?>

<div class="historial-container" style="margin-top: 32px;">
    <h2>Historial Reciente</h2>

    <?php if ($historialVacio): ?>
        <p class="muted historial-vacio-mensaje">Aún no hay registros en tu historial.</p>
    <?php else: ?>
        <ul class="historial-lista">
            <?php foreach ($historialPeso as $registro): ?>
                <li>
                    📅 <strong><?= date('d/m/Y', strtotime($registro['fecha'])) ?>:</strong>
                    Registraste un peso de <?= htmlspecialchars($registro['peso']) ?> kg.
                </li>
            <?php endforeach; ?>

            <?php foreach ($historialHabitos as $registro): ?>
                 <li>
                    📅 <strong><?= date('d/m/Y', strtotime($registro['fecha'])) ?>:</strong>
                    Check-in de hábitos:
                    <?php
                        $habitos = [];
                        if($registro['agua_cumplido']) $habitos[] = '💧 Agua';
                        if($registro['sueno_cumplido']) $habitos[] = '😴 Sueño';
                        if($registro['entrenamiento_cumplido']) $habitos[] = '🏋️ Entrenamiento';
                        echo empty($habitos) ? 'Ninguno marcado.' : implode(', ', $habitos) . '.';
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
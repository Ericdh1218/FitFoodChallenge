<?php /** @var array $ejercicio */ ?>

<a class="link" href="<?= url('/admin/ejercicios') ?>">&larr; Volver a Ejercicios</a>
<h1 style="margin-top: 8px;">Editar Ejercicio: <?= htmlspecialchars($ejercicio['nombre']) ?></h1>

<?php if (isset($_SESSION['flash_message'])): /* ... (código mensaje flash) ... */ endif; ?>

<form action="<?= url('/admin/ejercicios/update') ?>" method="POST" class="section">
    <input type="hidden" name="ejercicio_id" value="<?= $ejercicio['id'] ?>">
    
    <?php
    // Carga el formulario parcial (pasándole las variables)
    partial('admin/ejercicios/_form', [
        'ejercicio' => $ejercicio, // Pasa el ejercicio existente
        'grupos' => $grupos,
        'tipos' => $tipos,
        'equipos' => $equipos
    ]);
    ?>
    <button type="submit" class="btn primary" style="margin-top: 16px;">Guardar Cambios</button>
</form>

<div class="section" style="margin-top: 24px; border-color: var(--danger);">
    <h2>Eliminar Ejercicio</h2>
    <p class="muted">Esta acción es irreversible y borrará el ejercicio de todas las rutinas.</p>
    <form action="<?= url('/admin/ejercicios/delete') ?>" method="POST" onsubmit="return confirm('¿Estás SEGURO de que quieres eliminar este ejercicio?');">
        <input type="hidden" name="ejercicio_id" value="<?= $ejercicio['id'] ?>">
        <button type="submit" class="btn primary" style="background: var(--danger); border: none;">
            Eliminar Ejercicio Permanentemente
        </button>
    </form>
</div>
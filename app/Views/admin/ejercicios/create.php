<a class="link" href="<?= url('/admin/ejercicios') ?>">&larr; Volver a Ejercicios</a>
<h1 style="margin-top: 8px;">Crear Nuevo Ejercicio</h1>

<?php if (isset($_SESSION['flash_message'])): /* ... (código mensaje flash) ... */ endif; ?>

<form action="<?= url('/admin/ejercicios/store') ?>" method="POST" class="section">
    <?php
    // Carga el formulario parcial (pasándole las variables)
    partial('admin/ejercicios/_form', [
        'ejercicio' => $ejercicio ?? [], // Pasa un array vacío
        'grupos' => $grupos,
        'tipos' => $tipos,
        'equipos' => $equipos
    ]);
    ?>
    <button type="submit" class="btn primary" style="margin-top: 16px;">Crear Ejercicio</button>
</form>
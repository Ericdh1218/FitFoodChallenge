<?php 
/** @var array $rutina */
/** @var array $ejerciciosEnRutina */
/** @var array $ejerciciosDisponibles */
?>

<a class="link" href="<?= url('/admin/rutinas') ?>">&larr; Volver a Rutinas</a>
<h1 style="margin-top: 8px;">Editar Rutina: <?= htmlspecialchars($rutina['nombre_rutina']) ?></h1>

<?php if (isset($_SESSION['flash_message'])): /* ... (código mensaje flash) ... */ endif; ?>

<form action="<?= url('/admin/rutinas/update') ?>" method="POST" class="section">
    <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">
    
    <div class="form-group">
        <label for="nombre_rutina">Nombre de la Rutina</label>
        <input type="text" id="nombre_rutina" name="nombre_rutina" class="input" value="<?= htmlspecialchars($rutina['nombre_rutina']) ?>" required>
    </div>
    
    <div class="form-group">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" class="input" rows="4"><?= htmlspecialchars($rutina['descripcion']) ?></textarea>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
            <label for="nivel">Nivel</label>
            <select id="nivel" name="nivel" class="input" required>
                <option value="principiante" <?= $rutina['nivel'] == 'principiante' ? 'selected' : '' ?>>Principiante</option>
                <option value="intermedio" <?= $rutina['nivel'] == 'intermedio' ? 'selected' : '' ?>>Intermedio</option>
                <option value="avanzado" <?= $rutina['nivel'] == 'avanzado' ? 'selected' : '' ?>>Avanzado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="tipo_rutina">Tipo (Opcional)</label>
            <input type="text" id="tipo_rutina" name="tipo_rutina" class="input" value="<?= htmlspecialchars($rutina['tipo_rutina'] ?? '') ?>" placeholder="Ej: Fuerza, Cardio, Híbrido">
        </div>
    </div>
    
    <button type="submit" class="btn primary" style="margin-top: 16px;">Guardar Cambios</button>
</form>

<div class="section" style="margin-top: 24px;">
    <h2>Gestionar Ejercicios de la Rutina</h2>
    
    <div class="layout-sidebar-wrapper">
        <aside class="layout-sidebar">
            <h3>Ejercicios Actuales</h3>
            <?php if (empty($ejerciciosEnRutina)): ?>
                <p class="muted">Aún no hay ejercicios en esta rutina.</p>
            <?php else: ?>
                <table class="table">
                    <thead> <tr> <th>Ejercicio</th> <th>Series/Reps</th> <th></th> </tr> </thead>
                    <tbody>
                    <?php foreach ($ejerciciosEnRutina as $ej): ?>
                        <tr>
                            <td><?= htmlspecialchars($ej['nombre']) ?></td>
                            <td><?= htmlspecialchars($ej['series_reps']) ?></td>
                            <td>
                                <form action="<?= url('/admin/rutinas/quitar-ejercicio') // Ruta nueva ?>" method="POST">
                                    <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">
                                    <input type="hidden" name="ejercicio_id" value="<?= $ej['id'] ?>">
                                    <button type="submit" class="btn ghost btn-sm" style="color: var(--danger);">Quitar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </aside>

        <main class="layout-content">
            <h3>Añadir Ejercicio</h3>
            <form action="<?= url('/admin/rutinas/agregar-ejercicio') // Ruta nueva ?>" method="POST">
                <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">
                
                <div class="form-group">
                    <label for="ejercicio_id">Seleccionar Ejercicio</label>
                    <select id="ejercicio_id" name="ejercicio_id" class="input" required>
                        <option value="">-- Elige un ejercicio --</option>
                        <?php foreach ($ejerciciosDisponibles as $ej): ?>
                            <option value="<?= $ej['id'] ?>"><?= htmlspecialchars($ej['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="series_reps">Series y Repeticiones (Texto)</label>
                    <input type="text" id="series_reps" name="series_reps" class="input" placeholder="Ej: 3 series de 10 repeticiones" required>
                </div>
                
                <button type="submit" class="btn primary" style="width: 100%;">Añadir Ejercicio a la Rutina</button>
            </form>
        </main>
    </div>
</div>

<div class="section" style="margin-top: 24px; border-color: var(--danger);">
    <h2>Eliminar Rutina</h2>
    <form action="<?= url('/admin/rutinas/delete') ?>" method="POST" onsubmit="return confirm('¿Estás SEGURO de que quieres eliminar esta rutina?');">
        <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">
        <button type="submit" class="btn primary" style="background: var(--danger); border: none;">
            Eliminar Rutina Permanentemente
        </button>
    </form>
</div>
<a class="link" href="<?= url('/admin/rutinas') ?>">&larr; Volver a Rutinas</a>
<h1 style="margin-top: 8px;">Crear Nueva Rutina Predefinida</h1>

<?php if (isset($_SESSION['flash_message'])): /* ... (código mensaje flash) ... */ endif; ?>

<form action="<?= url('/admin/rutinas/store') ?>" method="POST" class="section">
    
    <div class="form-group">
        <label for="nombre_rutina">Nombre de la Rutina</label>
        <input type="text" id="nombre_rutina" name="nombre_rutina" class="input" required>
    </div>
    
    <div class="form-group">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" class="input" rows="4"></textarea>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
            <label for="nivel">Nivel</label>
            <select id="nivel" name="nivel" class="input" required>
                <option value="principiante">Principiante</option>
                <option value="intermedio">Intermedio</option>
                <option value="avanzado">Avanzado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="tipo_rutina">Tipo (Opcional)</label>
            <input type="text" id="tipo_rutina" name="tipo_rutina" class="input" placeholder="Ej: Fuerza, Cardio, Híbrido">
        </div>
    </div>
    
    <button type="submit" class="btn primary" style="margin-top: 16px;">Crear y Añadir Ejercicios</button>
</form>
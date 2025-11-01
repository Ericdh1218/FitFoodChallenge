<?php /** @var array $receta */ ?>

<a class="link" href="<?= url('/admin/recetas') ?>">&larr; Volver a Recetas</a>
<h1 style="margin-top: 8px;">Editar Receta: <?= htmlspecialchars($receta['titulo']) ?></h1>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="flash-info card" style="background: var(--danger); color: white; margin-bottom: 16px; padding: 16px; font-weight: 500;">
        <?= htmlspecialchars($_SESSION['flash_message']) ?>
        <?php unset($_SESSION['flash_message']); ?>
    </div>
<?php endif; ?>

<form action="<?= url('/admin/recetas/update') ?>" method="POST" class="section">
    <input type="hidden" name="receta_id" value="<?= $receta['id'] ?>">

    <div class="form-group">
        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" class="input" value="<?= htmlspecialchars($receta['titulo']) ?>">
    </div>
    
    <div class="form-group">
        <label for="descripcion">Descripción (Resumen Corto)</label>
        <textarea id="descripcion" name="descripcion" class="input" rows="3"><?= htmlspecialchars($receta['descripcion']) ?></textarea>
    </div>

    <div class="form-group">
        <label for="ingredientes">Ingredientes (Texto simple - para recetas predefinidas)</label>
        <textarea id="ingredientes" name="ingredientes" class="input" rows="5"><?= htmlspecialchars($receta['ingredientes']) ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="instrucciones">Instrucciones (Un paso por línea)</label>
        <textarea id="instrucciones" name="instrucciones" class="input" rows="8"><?= htmlspecialchars($receta['instrucciones']) ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria" class="input">
            <option value="pre entreno" <?= $receta['categoria'] == 'pre entreno' ? 'selected' : '' ?>>Pre Entreno</option>
            <option value="post entreno" <?= $receta['categoria'] == 'post entreno' ? 'selected' : '' ?>>Post Entreno</option>
            <option value="intra entreno" <?= $receta['categoria'] == 'intra entreno' ? 'selected' : '' ?>>Intra Entreno</logo>
            <option value="antes de dormir" <?= $receta['categoria'] == 'antes de dormir' ? 'selected' : '' ?>>Antes de Dormir</option>
            <option value="comida de descanso" <?= $receta['categoria'] == 'comida de descanso' ? 'selected' : '' ?>>Comida de Descanso</option>
        </select>
    </div>

    <hr style="border-color: var(--line); margin: 24px 0;">
    <h3>Filtros Especiales</h3>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        <div class="form-check-group">
            <input type="checkbox" id="es_barato" name="es_barato" value="1" <?= $receta['es_barato'] ? 'checked' : '' ?>>
            <label for="es_barato">💰 Económica</label>
        </div>
        <div class="form-check-group">
            <input type="checkbox" id="es_rapido" name="es_rapido" value="1" <?= $receta['es_rapido'] ? 'checked' : '' ?>>
            <label for="es_rapido">⏱️ Rápida</label>
        </div>
        <div class="form-check-group">
            <input type="checkbox" id="es_snack_estudio" name="es_snack_estudio" value="1" <?= $receta['es_snack_estudio'] ? 'checked' : '' ?>>
            <label for="es_snack_estudio">🧠 Snack Estudio</label>
        </div>
    </div>
    
    <hr style="border-color: var(--line); margin: 24px 0;">
    <h3>Info Nutricional Manual (Para recetas predefinidas)</h3>
    <p class="muted" style="font-size: 0.9em; margin-top: -10px; margin-bottom: 16px;">Dejar en 0 si es receta de usuario (se calculará automático).</p>
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px;">
        <div class="form-group">
            <label for="kcal_manual">Kcal</label>
            <input type="number" id="kcal_manual" name="kcal_manual" class="input" value="<?= $receta['kcal_manual'] ?>">
        </div>
         <div class="form-group">
            <label for="proteinas_g_manual">Proteínas (g)</label>
            <input type="number" step="0.1" id="proteinas_g_manual" name="proteinas_g_manual" class="input" value="<?= $receta['proteinas_g_manual'] ?>">
        </div>
         <div class="form-group">
            <label for="grasas_g_manual">Grasas (g)</label>
            <input type="number" step="0.1" id="grasas_g_manual" name="grasas_g_manual" class="input" value="<?= $receta['grasas_g_manual'] ?>">
        </div>
         <div class="form-group">
            <label for="carbos_g_manual">Carbos (g)</label>
            <input type="number" step="0.1" id="carbos_g_manual" name="carbos_g_manual" class="input" value="<?= $receta['carbos_g_manual'] ?>">
        </div>
         <div class="form-group">
            <label for="fibra_g_manual">Fibra (g)</label>
            <input type="number" step="0.1" id="fibra_g_manual" name="fibra_g_manual" class="input" value="<?= $receta['fibra_g_manual'] ?>">
        </div>
    </div>

    <button type="submit" class="btn primary" style="margin-top: 16px;">Guardar Cambios</button>
</form>

<div class="section" style="margin-top: 24px; border-color: var(--danger);">
    <h2>Eliminar Receta</h2>
    <p class="muted">Esta acción es irreversible.</p>
    <form action="<?= url('/admin/recetas/delete') ?>" method="POST" onsubmit="return confirm('¿Estás SEGURO de que quieres eliminar esta receta?');">
        <input type="hidden" name="receta_id" value="<?= $receta['id'] ?>">
        <button type="submit" class="btn primary" style="background: var(--danger); border: none;">
            Eliminar Receta Permanentemente
        </button>
    </form>
</div>
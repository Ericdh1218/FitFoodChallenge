<?php 
// Variables que esperamos:
/** @var array $ejercicio (datos existentes o array vacío) */
/** @var array $grupos (lista de grupos) */
/** @var array $tipos (lista de tipos) */
/** @var array $equipos (lista de equipos) */
?>

<div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" id="nombre" name="nombre" class="input" value="<?= htmlspecialchars($ejercicio['nombre'] ?? '') ?>" required>
</div>

<div class="form-group">
    <label for="descripcion">Descripción</label>
    <textarea id="descripcion" name="descripcion" class="input" rows="4"><?= htmlspecialchars($ejercicio['descripcion'] ?? '') ?></textarea>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
    <div class="form-group">
        <label for="media_url">Nombre Archivo Imagen (ej. sentadilla.jpg)</label>
        <input type="text" id="media_url" name="media_url" class="input" value="<?= htmlspecialchars($ejercicio['media_url'] ?? '') ?>" placeholder="ejercicio.jpg">
    </div>
    <div class="form-group">
        <label for="video_url">URL Video (YouTube Embed)</label>
        <input type="text" id="video_url" name="video_url" class="input" value="<?= htmlspecialchars($ejercicio['video_url'] ?? '') ?>" placeholder="https://youtube.com/embed/...">
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
    <div class="form-group">
        <label for="grupo_muscular">Grupo Muscular</label>
        <select id="grupo_muscular" name="grupo_muscular" class="input" required>
            <option value="">-- Seleccionar --</option>
            <?php foreach ($grupos as $g): ?>
            <option value="<?= $g ?>" <?= ($ejercicio['grupo_muscular'] ?? '') == $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="tipo_entrenamiento">Tipo</label>
        <select id="tipo_entrenamiento" name="tipo_entrenamiento" class="input">
             <option value="">-- Seleccionar --</option>
            <?php foreach ($tipos as $t): ?>
            <option value="<?= $t ?>" <?= ($ejercicio['tipo_entrenamiento'] ?? '') == $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="equipamiento">Equipamiento</label>
        <select id="equipamiento" name="equipamiento" class="input">
             <option value="">-- Seleccionar --</option>
            <?php foreach ($equipos as $e): ?>
            <option value="<?= $e ?>" <?= ($ejercicio['equipamiento'] ?? '') == $e ? 'selected' : '' ?>><?= $e ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<?php
/** @var string $tipo */
$h = fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');
?>
<h1><?= $h($title ?? 'Nueva Receta') ?></h1>

<form action="<?= url('/admin/recetas/store') ?>" method="POST" class="card" style="padding:16px; display:grid; gap:12px; max-width:760px;">
  <input type="hidden" name="tipo" value="<?= $h($tipo) ?>">

  <?php if ($tipo === 'user'): ?>
    <label>Usuario (ID)
      <input type="number" name="user_id" min="1" required>
    </label>
  <?php endif; ?>

  <label>Título <input type="text" name="titulo" required></label>
  <label>Categoría <input type="text" name="categoria"></label>
  <label>Descripción <textarea name="descripcion" rows="3"></textarea></label>
  <label>Instrucciones <textarea name="instrucciones" rows="6"></textarea></label>
  <label>Imagen URL <input type="file" name="imagen_url" accept=".png,.jpg,.jpeg" class="form-control"></label>

  <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:8px;">
    <label>Kcal <input type="number" step="1" name="kcal"></label>
    <label>Prote <input type="number" step="0.1" name="prote"></label>
    <label>Carbs <input type="number" step="0.1" name="carbs"></label>
    <label>Grasas <input type="number" step="0.1" name="grasas"></label>
  </div>

  <button class="btn primary" type="submit">Guardar</button>
  <a class="btn ghost" href="<?= url('/admin/recetas') ?>">Cancelar</a>
</form>

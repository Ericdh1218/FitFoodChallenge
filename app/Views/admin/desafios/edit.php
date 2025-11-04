<?php /** @var array $d */ ?>
<h1>Editar Desafío</h1>
<form action="<?= url('/admin/desafios/update') ?>" method="POST" class="card" style="max-width:760px">
  <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">

  <label>Código (único)</label>
  <input name="codigo" value="<?= htmlspecialchars($d['codigo']) ?>" required>

  <label>Título</label>
  <input name="titulo" value="<?= htmlspecialchars($d['titulo']) ?>" required>

  <label>Descripción</label>
  <textarea name="descripcion" rows="3"><?= htmlspecialchars($d['descripcion'] ?? '') ?></textarea>

  <div style="display:flex; gap:12px;">
    <div style="flex:1">
      <label>Duración (días)</label>
      <input type="number" name="duracion_dias" value="<?= (int)$d['duracion_dias'] ?>" min="1" required>
    </div>
    <div style="flex:1">
      <label>Recompensa XP</label>
      <input type="number" name="recompensa_xp" value="<?= (int)$d['recompensa_xp'] ?>" min="0" required>
    </div>
  </div>

  <label>Tipo/Hábito vinculado (opcional)</label>
  <input name="tipo_habito_link" value="<?= htmlspecialchars($d['tipo_habito_link'] ?? '') ?>">

  <div style="margin-top:16px;">
    <button class="btn primary">Actualizar</button>
    <a class="btn ghost" href="<?= url('/admin/desafios') ?>">Volver</a>
  </div>
</form>

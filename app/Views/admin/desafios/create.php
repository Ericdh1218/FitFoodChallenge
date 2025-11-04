<h1>Nuevo Desafío</h1>
<form action="<?= url('/admin/desafios/store') ?>" method="POST" class="card" style="max-width:760px">
  <label>Código (único)</label>
  <input name="codigo" required placeholder="p.ej. beber-agua-7d">

  <label>Título</label>
  <input name="titulo" required>

  <label>Descripción</label>
  <textarea name="descripcion" rows="3"></textarea>

  <div style="display:flex; gap:12px;">
    <div style="flex:1">
      <label>Duración (días)</label>
      <input type="number" name="duracion_dias" value="7" min="1" required>
    </div>
    <div style="flex:1">
      <label>Recompensa XP</label>
      <input type="number" name="recompensa_xp" value="50" min="0" required>
    </div>
  </div>

  <label>Tipo/Hábito vinculado (opcional)</label>
  <input name="tipo_habito_link" placeholder="p.ej. agua, pasos, sueño...">

  <div style="margin-top:16px;">
    <button class="btn primary">Guardar</button>
    <a class="btn ghost" href="<?= url('/admin/desafios') ?>">Cancelar</a>
  </div>
</form>

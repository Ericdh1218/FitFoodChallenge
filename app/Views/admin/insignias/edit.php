<h1>Nueva Insignia</h1>
<form action="<?= url('/admin/insignias/store') ?>" method="POST" enctype="multipart/form-data" class="card" style="max-width:680px;">
  <label class="form-label">Código</label>
  <input class="input" name="codigo" required placeholder="ej: primer-checkin">

  <label class="form-label">Nombre</label>
  <input class="input" name="nombre" required placeholder="ej: Primer check-in">

  <label class="form-label">Descripción</label>
  <textarea class="input" name="descripcion" rows="3"></textarea>

  <div class="grid" style="grid-template-columns:1fr 1fr; gap:12px;">
    <div>
      <label class="form-label">XP recompensa</label>
      <input class="input" type="number" name="xp_recompensa" value="0" min="0">
    </div>
    <div>
      <label class="form-label">Icono (PNG/JPG/WebP/SVG)</label>
      <input class="input" type="file" name="icono_url" accept=".png,.jpg,.jpeg,.webp,.svg">
    </div>
  </div>

  <div style="margin-top:16px;">
    <button class="btn primary" type="submit">Guardar</button>
    <a class="btn ghost" href="<?= url('/admin/insignias') ?>">Cancelar</a>
  </div>
</form>

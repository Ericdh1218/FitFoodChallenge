<?php
/** @var array $predefinidas */
/** @var array $deUsuarios */
$h = fn($v)=>htmlspecialchars((string)($v??''),ENT_QUOTES,'UTF-8');
?>

<h1>Gestionar Recetas</h1>
<p class="muted">Lista de todas las recetas (predefinidas y de usuarios).</p>

<div class="tabs" style="margin-top:16px;">
  <button class="btn ghost" id="tabPred" onclick="showTab('pred')">Predefinidas</button>
  <button class="btn ghost" id="tabUsers" onclick="showTab('users')">De usuarios</button>
</div>

<!-- PREDEFINIDAS -->
<div id="panelPred" class="card" style="margin-top:12px;">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h3>Recetas predefinidas</h3>
    <a href="<?= url('/admin/recetas/create?tipo=pred') ?>" class="btn primary">+ Nueva predefinida</a>
  </div>

  <table class="table" style="margin-top:10px;">
    <thead>
      <tr>
        <th>ID</th><th>Título</th><th>Categoría</th><th style="width:260px;">Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($predefinidas)): ?>
      <tr><td colspan="4" class="muted">No hay recetas predefinidas.</td></tr>
    <?php else: foreach ($predefinidas as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= $h($r['titulo']) ?></td>
        <td><?= $h($r['categoria'] ?? '') ?></td>
        <td>
          <a class="btn ghost" href="<?= url('/admin/recetas/create?tipo=pred&id='.(int)$r['id']) ?>">Duplicar (como nueva predef.)</a>

          <!-- Clonar a un usuario -->
          <form action="<?= url('/admin/recetas/clonar') ?>" method="POST" style="display:inline-flex; gap:6px; align-items:center;">
            <input type="hidden" name="receta_id" value="<?= (int)$r['id'] ?>">
            <input type="number" name="user_id" min="1" placeholder="ID usuario" style="width:120px;">
            <button class="btn primary" type="submit">Clonar a usuario</button>
          </form>

          <form action="<?= url('/admin/recetas/delete') ?>" method="POST" style="display:inline-block" onsubmit="return confirm('¿Eliminar esta receta predefinida?');">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn danger" type="submit">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<!-- DE USUARIOS -->
<div id="panelUsers" class="card" style="margin-top:12px; display:none;">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h3>Recetas de usuarios</h3>
    <a href="<?= url('/admin/recetas/create?tipo=user') ?>" class="btn primary">+ Nueva de usuario</a>
  </div>

  <table class="table" style="margin-top:10px;">
    <thead>
      <tr>
        <th>ID</th><th>Título</th><th>Autor</th><th>Categoría</th><th style="width:160px;">Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($deUsuarios)): ?>
      <tr><td colspan="5" class="muted">No hay recetas de usuarios.</td></tr>
    <?php else: foreach ($deUsuarios as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= $h($r['titulo']) ?></td>
        <td><?= $h($r['autor_nombre'] ?? '—') ?> <span class="muted" style="font-size:.85em;"><?= $h($r['autor_email'] ?? '') ?></span></td>
        <td><?= $h($r['categoria'] ?? '') ?></td>
        <td>
          <a class="btn ghost" href="<?= url('/admin/recetas/create?tipo=user&id='.(int)$r['id']) ?>">Duplicar</a>
          <form action="<?= url('/admin/recetas/delete') ?>" method="POST" style="display:inline-block" onsubmit="return confirm('¿Eliminar esta receta?');">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn danger" type="submit">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<script>
function showTab(which){
  const tabPred  = document.getElementById('tabPred');
  const tabUsers = document.getElementById('tabUsers');
  const panelPred  = document.getElementById('panelPred');
  const panelUsers = document.getElementById('panelUsers');
  if (which === 'pred') {
    panelPred.style.display = '';
    panelUsers.style.display = 'none';
    tabPred.classList.add('primary'); tabUsers.classList.remove('primary');
  } else {
    panelPred.style.display = 'none';
    panelUsers.style.display = '';
    tabUsers.classList.add('primary'); tabPred.classList.remove('primary');
  }
}
showTab('pred');
</script>

<?php
/** @var array $predisenadas */
/** @var array $deUsuarios */

// helper para escapar seguro (tolera null)
$h = fn($v) => htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');

// recorta con mb_strimwidth si está disponible
$trim = function (?string $txt, int $w = 80) use ($h) {
    $txt = $txt ?? '';
    if (function_exists('mb_strimwidth')) {
        $txt = mb_strimwidth($txt, 0, $w, '…', 'UTF-8');
    } else {
        $txt = (strlen($txt) > $w) ? substr($txt, 0, $w - 2) . '…' : $txt;
    }
    return $h($txt);
};
?>

<h1>Gestionar Rutinas</h1>
<p class="muted">Administra rutinas prediseñadas y las creadas por usuarios.</p>

<div class="tabs" style="margin-top:16px;">
  <button class="btn ghost" id="tabPred"  onclick="showTab('pred')">Prediseñadas</button>
  <button class="btn ghost" id="tabUsers" onclick="showTab('users')">De usuarios</button>
</div>

<!-- Prediseñadas -->
<div id="panelPred" class="card" style="margin-top:12px;">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h3>Rutinas prediseñadas</h3>
    <a href="<?= url('/admin/rutinas/create?tipo=pred') ?>" class="btn primary">+ Nueva</a>
  </div>

  <table class="table" style="margin-top:10px;">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Nivel</th>
        <th>Descripción</th>
        <th style="width:160px;">Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($predisenadas)): ?>
      <tr><td colspan="5" class="muted">No hay rutinas prediseñadas.</td></tr>
    <?php else: ?>
      <?php foreach ($predisenadas as $r): ?>
        <tr>
          <td><?= (int)($r['id'] ?? 0) ?></td>
          <td><?= $h($r['nombre_rutina'] ?? '') ?></td>
          <td><?= $h($r['nivel'] ?? '') ?></td>
          <td><?= $trim($r['descripcion'] ?? '') ?></td>
          <td>
            <a class="btn ghost" href="<?= url('/admin/rutinas/edit?id='.(int)($r['id'] ?? 0).'&tipo=pred') ?>">Editar</a>
            <form action="<?= url('/admin/rutinas/delete') ?>" method="POST" style="display:inline-block" onsubmit="return confirm('¿Eliminar esta rutina prediseñada?');">
              <input type="hidden" name="id"   value="<?= (int)($r['id'] ?? 0) ?>">
              <input type="hidden" name="tipo" value="pred">
              <button class="btn danger" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- De Usuarios -->
<div id="panelUsers" class="card" style="margin-top:12px; display:none;">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h3>Rutinas de usuarios</h3>
    <a href="<?= url('/admin/rutinas/create?tipo=user') ?>" class="btn primary">+ Nueva</a>
  </div>

  <table class="table" style="margin-top:10px;">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Nivel</th>
        <th>Autor</th>
        <th>Descripción</th>
        <th style="width:200px;">Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($deUsuarios)): ?>
      <tr><td colspan="6" class="muted">No hay rutinas de usuarios.</td></tr>
    <?php else: ?>
      <?php foreach ($deUsuarios as $r): ?>
        <tr>
          <td><?= (int)($r['id'] ?? 0) ?></td>
          <td><?= $h($r['nombre_rutina'] ?? '') ?></td>
          <td><?= $h($r['nivel'] ?? '') ?></td>
          <td>
            <?= $h($r['autor_nombre'] ?? '—') ?><br>
            <span class="muted" style="font-size:.85em;"><?= $h($r['autor_email'] ?? '') ?></span>
          </td>
          <td><?= $trim($r['descripcion'] ?? '') ?></td>
          <td>
            <a class="btn ghost" href="<?= url('/admin/rutinas/edit?id='.(int)($r['id'] ?? 0).'&tipo=user') ?>">Editar</a>
            <a class="btn ghost" href="<?= url('/admin/rutinas/edit?id='.(int)($r['id'] ?? 0).'&tipo=user#ejercicios') ?>">Ejercicios</a>
            <form action="<?= url('/admin/rutinas/delete') ?>" method="POST" style="display:inline-block" onsubmit="return confirm('¿Eliminar esta rutina de usuario?');">
              <input type="hidden" name="id"   value="<?= (int)($r['id'] ?? 0) ?>">
              <input type="hidden" name="tipo" value="user">
              <button class="btn danger" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
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

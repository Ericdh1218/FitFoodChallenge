<?php /** @var array $insignias */ ?>
<h1>Insignias</h1>
<p class="muted">Crea, edita o elimina insignias. La imagen se almacena en <code>public/img/insignias</code>.</p>

<div style="margin:12px 0;">
  <a class="btn primary" href="<?= url('/admin/insignias/create') ?>">+ Nueva Insignia</a>
</div>

<table class="table">
  <thead>
    <tr>
      <th>ID</th><th>Código</th><th>Nombre</th><th>XP</th><th>Icono</th><th style="width:180px;">Acciones</th>
    </tr>
  </thead>
  <tbody>
  <?php if (empty($insignias)): ?>
    <tr><td colspan="6" class="muted">No hay insignias.</td></tr>
  <?php else: foreach($insignias as $b): ?>
    <tr>
      <td><?= (int)$b['id'] ?></td>
      <td><code><?= htmlspecialchars($b['codigo']) ?></code></td>
      <td><?= htmlspecialchars($b['nombre']) ?></td>
      <td><?= (int)$b['xp_recompensa'] ?></td>
      <td>
        <?php if(!empty($b['icono_url'])): ?>
          <img src="<?= url('/'.$b['icono_url']) ?>" alt="" style="height:36px;object-fit:contain;">
        <?php else: ?>
          <span class="muted">—</span>
        <?php endif; ?>
      </td>
      <td>
        <a class="btn ghost" href="<?= url('/admin/insignias/edit?id='.(int)$b['id']) ?>">Editar</a>
        <form action="<?= url('/admin/insignias/delete') ?>" method="POST" style="display:inline-block" onsubmit="return confirm('¿Eliminar insignia?');">
          <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
          <button class="btn danger" type="submit">Eliminar</button>
        </form>
      </td>
    </tr>
  <?php endforeach; endif; ?>
  </tbody>
</table>

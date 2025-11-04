<?php /** @var array $desafios */ ?>
<h1>Gestionar Desafíos</h1>
<p class="muted">Crea, edita o elimina desafíos y define los puntos (XP).</p>

<div class="toolbar">
  <a class="btn primary" href="<?= url('/admin/desafios/create') ?>">+ Nuevo Desafío</a>
</div>

<div class="table-responsive">
  <table class="table">
    <thead>
      <tr>
        <th class="col-hide-sm">ID</th>
        <th>Código</th>
        <th>Título</th>
        <th class="col-hide-sm">Duración (d)</th>
        <th>XP</th>
        <th class="col-hide-sm">Hábito</th>
        <th class="col-hide-sm">Participantes</th>
        <th data-nowrap>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($desafios)): ?>
      <tr><td colspan="8" class="muted">No hay desafíos.</td></tr>
    <?php else: foreach ($desafios as $r): ?>
      <tr>
        <td class="col-hide-sm"><?= (int)$r['id'] ?></td>
        <td><code><?= htmlspecialchars($r['codigo']) ?></code></td>
        <td><?= htmlspecialchars($r['titulo']) ?></td>
        <td class="col-hide-sm"><?= (int)$r['duracion_dias'] ?></td>
        <td><?= (int)$r['recompensa_xp'] ?></td>
        <td class="col-hide-sm"><?= htmlspecialchars($r['tipo_habito_link'] ?: '—') ?></td>
        <td class="col-hide-sm"><?= (int)($r['participantes'] ?? 0) ?></td>
        <td data-nowrap>
          <a class="btn ghost btn-sm" href="<?= url('/admin/desafios/edit?id='.(int)$r['id']) ?>">Editar</a>
          <form action="<?= url('/admin/desafios/delete') ?>" method="POST" class="inline-form" onsubmit="return confirm('¿Eliminar desafío?');">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn danger btn-sm" type="submit">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

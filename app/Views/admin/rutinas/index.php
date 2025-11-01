<?php /** @var array $rutinas */ ?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Gestionar Rutinas</h1>
    <a href="<?= url('/admin/rutinas/create') ?>" class="btn primary">+ Nueva Rutina Predefinida</a>
</div>
<p class="muted">Lista de todas las rutinas (predefinidas y de usuarios).</p>

<?php if (isset($_SESSION['flash_message'])): /* ... (código mensaje flash) ... */ endif; ?>

<div class="table-container" style="margin-top: 24px;">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Autor</th>
                <th>Nivel</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rutinas as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['nombre_rutina']) ?></td>
                <td>
                    <?php if ($r['user_id']): ?>
                        <span class="tag"><?= htmlspecialchars($r['autor_nombre'] ?? 'ID: '.$r['user_id']) ?></span>
                    <?php else: ?>
                        <span class="tag" style="background: var(--brand-2); color: var(--bg);">Predefinida</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['nivel']) ?></td>
                <td>
                    <a href="<?= url('/admin/rutinas/edit?id=' . $r['id']) ?>" class="btn ghost btn-sm" style="color: var(--brand-2); border-color: var(--brand-2);">
                        Editar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
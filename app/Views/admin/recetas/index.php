<?php /** @var array $recetas */ ?>

<h1>Gestionar Recetas</h1>
<p class="muted">Lista de todas las recetas (predefinidas y de usuarios).</p>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="flash-info card" style="background: var(--brand); color: var(--bg); margin-bottom: 16px; padding: 16px; font-weight: 500;">
        <?= htmlspecialchars($_SESSION['flash_message']) ?>
        <?php unset($_SESSION['flash_message']); ?>
    </div>
<?php endif; ?>

<div class="table-container" style="margin-top: 24px;">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recetas as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['titulo']) ?></td>
                <td>
                    <?php if ($r['user_id']): ?>
                        <span class="tag"><?= htmlspecialchars($r['autor_nombre'] ?? 'ID: '.$r['user_id']) ?></span>
                    <?php else: ?>
                        <span class="tag" style="background: var(--brand-2); color: var(--bg);">Predefinida</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['categoria']) ?></td>
                <td>
                    <a href="<?= url('/admin/recetas/edit?id=' . $r['id']) ?>" class="btn ghost btn-sm" style="color: var(--brand-2); border-color: var(--brand-2);">
                        Editar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
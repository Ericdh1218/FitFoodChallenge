<?php /** @var array $ejercicios */ ?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Gestionar Ejercicios</h1>
    <a href="<?= url('/admin/ejercicios/create') ?>" class="btn primary">+ Nuevo Ejercicio</a>
</div>
<p class="muted">Lista de todos los ejercicios predefinidos.</p>

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
                <th>Nombre</th>
                <th>Grupo Muscular</th>
                <th>Equipamiento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ejercicios as $ej): ?>
            <tr>
                <td><?= $ej['id'] ?></td>
                <td><?= htmlspecialchars($ej['nombre']) ?></td>
                <td><?= htmlspecialchars($ej['grupo_muscular']) ?></td>
                <td><?= htmlspecialchars($ej['equipamiento']) ?></td>
                <td>
                    <a href="<?= url('/admin/ejercicios/edit?id=' . $ej['id']) ?>" class="btn ghost btn-sm" style="color: var(--brand-2); border-color: var(--brand-2);">
                        Editar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
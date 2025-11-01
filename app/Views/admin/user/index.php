<?php /** @var array $usuarios */ ?>

<h1>Gestionar Usuarios</h1>
<p class="muted">Lista de todos los usuarios registrados en la plataforma.</p>

<?php 
$mensajeFlash = null;
if (isset($_SESSION['flash_message'])) {
    $mensajeFlash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
?>
<?php if ($mensajeFlash): ?>
    <div class="flash-info card" style="background: var(--brand); color: var(--bg); margin-bottom: 16px; padding: 16px; font-weight: 500;">
        <?= htmlspecialchars($mensajeFlash) ?>
    </div>
<?php endif; ?>
<div class="table-container" style="margin-top: 24px;">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Nivel</th>
                <th>XP</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['correo']) ?></td>
                <td>
                    <?php if ($u['tipo_user'] == 0): ?>
                        <span class="tag" style="background: var(--brand); color: var(--bg);">Admin</span>
                    <?php else: ?>
                        <span class="tag">Usuario</span>
                    <?php endif; ?>
                </td>
                <td><?= $u['level'] ?></td>
                <td><?= $u['xp'] ?></td>
                <td>
                    <a href="<?= url('/admin/users/edit?id=' . $u['id']) ?>" class="btn ghost btn-sm" style="color: var(--brand-2); border-color: var(--brand-2);">
                        Editar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
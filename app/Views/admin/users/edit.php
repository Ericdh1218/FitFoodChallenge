<?php /** @var array $usuario */ ?>

<a class="link" href="<?= url('/admin/users') ?>">&larr; Volver a Usuarios</a>
<h1 style="margin-top: 8px;">Editar Usuario: <?= htmlspecialchars($usuario['nombre']) ?></h1>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="flash-info card" style="background: var(--danger); color: white; margin-bottom: 16px; padding: 16px; font-weight: 500;">
        <?= htmlspecialchars($_SESSION['flash_message']) ?>
        <?php unset($_SESSION['flash_message']); ?>
    </div>
<?php endif; ?>

<form action="<?= url('/admin/users/update') ?>" method="POST" class="section">
    <input type="hidden" name="user_id" value="<?= $usuario['id'] ?>">

    <div class="form-group">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" class="input" value="<?= htmlspecialchars($usuario['nombre']) ?>">
    </div>
    
    <div class="form-group">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" class="input" value="<?= htmlspecialchars($usuario['correo']) ?>">
    </div>

    <hr style="border-color: var(--line); margin: 24px 0;">
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        <div class="form-group">
            <label for="level">Nivel</label>
            <input type="number" id="level" name="level" class="input" value="<?= $usuario['level'] ?>">
        </div>
        <div class="form-group">
            <label for="xp">XP</label>
            <input type="number" id="xp" name="xp" class="input" value="<?= $usuario['xp'] ?>">
        </div>
        <div class="form-group">
            <label for="tipo_user">Rol (0=Admin, 1=Usuario)</label>
            <input type="number" id="tipo_user" name="tipo_user" class="input" value="<?= $usuario['tipo_user'] ?>" min="0" max="1">
        </div>
    </div>

    <button type="submit" class="btn primary" style="margin-top: 16px;">Guardar Cambios</button>
</form>

<div class="section" style="margin-top: 24px; border-color: var(--danger);">
    <h2>Eliminar Usuario</h2>
    <p class="muted">Esta acción es irreversible y borrará al usuario y todos sus datos asociados.</p>
    <form action="<?= url('/admin/users/delete') ?>" method="POST" onsubmit="return confirm('¿Estás SEGURO de que quieres eliminar a este usuario? Esta acción no se puede deshacer.');">
        <input type="hidden" name="user_id" value="<?= $usuario['id'] ?>">
        <button type="submit" class="btn primary" style="background: var(--danger); border: none;">
            Eliminar Usuario Permanentemente
        </button>
    </form>
</div>
<div class="auth-card-wrapper">
    <div class="auth-card">
        
        <h1 class="auth-title">Iniciar Sesión</h1>
        <p class="auth-subtitle">Ingresa a tu cuenta de FitFood Challenge.</p>
        
        <?php if (!empty($error)): ?>
            <div class="form-error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('/login') ?>" method="POST" style="margin-top: 24px;">
            
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" class="input" placeholder="tu@correo.com" required>
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" class="input" placeholder="Tu contraseña" required>
            </div>

            <button type="submit" class="btn primary" style="width:100%; margin-top:16px; padding-top: 14px; padding-bottom: 14px; font-size: 16px;">
                Entrar
            </button>
        </form>

        <p class="auth-footer-link">
            ¿Aún no tienes cuenta? <a href="<?= url('/registro') ?>">Regístrate aquí</a>
        </p>
    </div>
</div>
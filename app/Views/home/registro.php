<div class="auth-card-wrapper">
    <div class="auth-card">
        
        <h1 class="auth-title">Crear una cuenta</h1>
        <p class="auth-subtitle">Únete a FitFood Challenge y empieza a crear hábitos saludables.</p>
        
        <form action="<?= url('/registro') ?>" method="POST" style="margin-top: 24px;">
            
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="input" placeholder="Tu nombre completo" required>
            </div>
            
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" class="input" placeholder="tu@correo.com" required>
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" class="input" placeholder="Mínimo 6 caracteres" required>
            </div>

            <div class="form-group">
                <label for="edad">Edad</label>
                <input type="number" id="edad" name="edad" class="input" placeholder="Tu edad" required>
            </div>

            <div class="form-group">
                <label for="genero">Género</label>
                <select id="genero" name="genero" class="input" required>
                    <option value="" disabled selected>Selecciona tu género</option>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                </select>
            </div>

            <button type="submit" class="btn primary" style="width:100%; margin-top:16px; padding-top: 14px; padding-bottom: 14px; font-size: 16px;">
                Crear mi cuenta
            </button>
        </form>

        <p class="auth-footer-link">
            ¿Ya tienes una cuenta? <a href="<?= url('/login') ?>">Inicia sesión aquí</a>
        </p>
    </div>
</div>
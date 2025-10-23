<?php /** @var string|null $error */ ?>

<div class="auth-card-wrapper">
    <div class="auth-card">
        
        <h1 class="auth-title">Crear Nueva Rutina</h1>
        <p class="auth-subtitle">Dale un nombre descriptivo a tu rutina.</p>
        
        <?php if (!empty($error)): ?>
            <div class="form-error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('/rutinas/crear') ?>" method="POST" style="margin-top: 24px;">
            
            <div class="form-group">
                <label for="nombre_rutina">Nombre de la Rutina</label>
                <input type="text" id="nombre_rutina" name="nombre_rutina" class="input" 
                       placeholder="Ej: Lunes - Pecho y Tríceps" required>
            </div>

            <button type="submit" class="btn primary" style="width:100%; margin-top:16px;">
                Guardar y Añadir Ejercicios
            </button>
        </form>

         <p class="auth-footer-link" style="margin-top: 20px;">
            <a href="<?= url('/rutinas') ?>">Cancelar</a>
        </p>
    </div>
</div>
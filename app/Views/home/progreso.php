<?php /** @var array $usuario */ ?>

<h1 style="margin-bottom: 8px;">Mi Progreso</h1>
<p class="muted">Registra aquí tus hábitos diarios para ganar puntos.</p>

<div class="auth-card-wrapper" style="padding-top: 16px;">
    <div class="auth-card" style="background: var(--panel); border: 1px solid var(--line);">
        
        <h2 class="auth-title" style="color: var(--text); font-size: 24px;">Check-in de Hoy</h2>
        <p class="auth-subtitle" style="color: var(--muted);">
            ¿Cómo te fue hoy, <?= htmlspecialchars($usuario['nombre']) ?>?
        </p>
        
        <form action="<?= url('/progreso/checkin') ?>" method="POST" style="margin-top: 24px;">
            
            <div class="form-check-group">
                <input type="checkbox" id="agua" name="agua_cumplido" value="1">
                <label for="agua">
                    <strong>Agua:</strong> 
                    <span>Completaste tu meta de <?= (int)($usuario['consumo_agua'] ?? 0) ?> vasos.</span>
                </label>
            </div>

            <div class="form-check-group">
                <input type="checkbox" id="sueno" name="sueno_cumplido" value="1">
                <label for="sueno">
                    <strong>Sueño:</strong> 
                    <span>Dormiste tus <?= (int)($usuario['horas_sueno'] ?? 0) ?> horas.</span>
                </label>
            </div>

            <div class="form-check-group">
                <input type="checkbox" id="entrenamiento" name="entrenamiento_cumplido" value="1">
                <label for="entrenamiento">
                    <strong>Entrenamiento:</strong> 
                    <span>Realizaste tu rutina de ejercicio de hoy.</span>
                </label>
            </div>
            
            <button type="submit" class="btn primary" style="width:100%; margin-top: 24px;">
                Guardar mi Progreso ✅
            </button>
        </form>
    </div>
</div>
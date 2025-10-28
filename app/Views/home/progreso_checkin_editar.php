<?php 
/** @var array $usuario */ 
/** @var array $registroHoy */ 
?>

<a class="link" href="<?= url('/progreso') ?>">← Volver a Progreso</a>

<div class="section" style="margin-top: 16px;"> 
    
    <h2 class="auth-title" style="color: var(--text); font-size: 24px;">Editar Check-in (<?= date('d/m/Y') ?>)</h2>
    
    <p class="auth-subtitle" style="color: var(--muted);">
        Ajusta los hábitos que completaste hoy.
    </p>
    
    <form action="<?= url('/progreso/checkin') ?>" method="POST" style="margin-top: 24px;">
        
        <div class="form-check-group">
            <input type="checkbox" id="agua" name="agua_cumplido" value="1"
                <?= !empty($registroHoy['agua_cumplido']) ? 'checked' : '' ?> > <label for="agua">
                <strong>Agua:</strong> 
                <span>Completaste tu meta de <?= (int)($usuario['consumo_agua'] ?? 0) ?> vasos.</span>
            </label>
        </div>
        
        <div class="form-check-group">
            <input type="checkbox" id="sueno" name="sueno_cumplido" value="1"
                <?= !empty($registroHoy['sueno_cumplido']) ? 'checked' : '' ?> > <label for="sueno">
                <strong>Sueño:</strong> 
                <span>Dormiste tus <?= (int)($usuario['horas_sueno'] ?? 0) ?> horas.</span>
            </label>
        </div>
        
        <div class="form-check-group">
            <input type="checkbox" id="entrenamiento" name="entrenamiento_cumplido" value="1"
                <?= !empty($registroHoy['entrenamiento_cumplido']) ? 'checked' : '' ?> > <label for="entrenamiento">
                <strong>Entrenamiento:</strong> 
                <span>Realizaste tu rutina de ejercicio de hoy.</span>
            </label>
        </div>
        
        <button type="submit" class="btn primary" style="width:100%; margin-top: 24px;">
            Actualizar mi Progreso ✅
        </button>
    </form>
    
</div>
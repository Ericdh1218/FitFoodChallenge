<?php 
/** @var array $desafio */
/** @var array|null $progreso */
/** @var array $ejercicios */

// Determina el progreso actual (0 si no se ha unido)
$diasCompletados = $progreso['progreso_actual'] ?? 0;
$diasTotales = $desafio['duracion_dias'];
$estaInscrito = $progreso !== null;
$estaCompletado = $progreso['completado'] ?? 0;
?>

<a class="link" href="<?= url('/') ?>">&larr; Volver a todos los Desafíos</a>

<div class="plan-header" style="text-align: center; margin: 16px 0 32px 0;">
    <h1 style="margin: 8px 0;"><?= htmlspecialchars($desafio['titulo']) ?></h1>
    <p class="muted"><?= htmlspecialchars($desafio['descripcion']) ?></p>
    <div>
        <span class="tag">Duración: <?= $diasTotales ?> días</span>
        <span class="tag" style="background: var(--brand); color: var(--bg);">Recompensa: <?= $desafio['recompensa_xp'] ?> XP</span>
    </div>
</div>

<div class="section desafio-container">
    
    <h2>Tu Progreso</h2>
    <?php if (!$estaInscrito): ?>
        <p class="muted">Aún no te has unido a este desafío.</p>
    <?php else: ?>
        <p class="muted">¡Sigue así! Has completado <?= $diasCompletados ?> de <?= $diasTotales ?> días.</p>
        
        <div class="progress-grid">
            <?php for ($i = 1; $i <= $diasTotales; $i++): ?>
                <?php
                $claseDia = '';
                if ($i <= $diasCompletados) {
                    $claseDia = 'day-complete'; // Día completado
                } elseif ($i == $diasCompletados + 1) {
                    $claseDia = 'day-current'; // Día actual (siguiente a completar)
                } else {
                    $claseDia = 'day-pending'; // Días futuros
                }
                ?>
                <div class="progress-day <?= $claseDia ?>">
                    <span class="day-number">Día <?= $i ?></span>
                    <?php if ($claseDia === 'day-complete'): ?>
                        <span>✓</span>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

    <div style="margin-top: 32px; text-align: center;">
        <?php if ($estaCompletado): ?>
            <p class="tag tag-verde" style="font-size: 1.1rem; padding: 12px 16px;">¡FELICIDADES! HAS COMPLETADO ESTE DESAFÍO 🏆</p>
        
        <?php elseif ($estaInscrito): ?>
            <h3>Registra tu Actividad de Hoy</h3>
            <p class="reto-texto">Ve a tu panel de progreso y marca el check-in de hoy para avanzar.</p>
            <a href="<?= url('/progreso') ?>" class="btn primary">Registrar mi progreso</a>
        
        <?php else: // Si no está inscrito ?>
            <form action="<?= url('/desafios/unirse') ?>" method="POST">
                <input type="hidden" name="desafio_id" value="<?= $desafio['id'] ?>">
                <button type="submit" class="btn primary" style="width: 100%; max-width: 400px; padding: 14px;">¡Unirme al Desafío!</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($desafio['codigo'] === 'reto-7x7' && !empty($ejercicios)): ?>
        <div style="margin-top: 40px; border-top: 1px solid var(--line); padding-top: 24px;">
            <h3>Ideas de Ejercicios (Sin Equipo)</h3>
            <div class="guia-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
                <?php partial('home/_partial_ejercicio_cards', ['ejercicios' => $ejercicios]); ?>
            </div>
        </div>
    <?php endif; ?>
    
</div>

<style>
.desafio-container {
    max-width: 900px;
    margin: 0 auto;
}
.tag-verde {
    background: var(--brand);
    color: var(--bg);
    border-color: var(--brand);
    font-weight: 600;
}
.reto-texto {
    color: var(--text);
    font-size: 1.05rem;
    line-height: 1.6;
    margin: 16px 0;
}

/* Grid de Progreso */
.progress-grid {
    display: grid;
    grid-template-columns: repeat(<?= $diasTotales ?>, 1fr); 
    gap: 8px;
    margin-top: 16px;
}
.progress-day {
    padding: 10px 5px;
    border-radius: var(--radius-sm);
    text-align: center;
    font-size: 0.9rem;
    font-weight: 600;
}
.progress-day .day-number {
    display: block;
    font-size: 0.8rem;
    font-weight: 400;
    /* === CAMBIO AQUÍ: hereda el color o usa --text === */
    color: inherit; 
    /* color: var(--text); */ /* <-- Opción alternativa */
    /* ================================================ */
    margin-bottom: 4px;
    opacity: 0.8; /* Lo hacemos un poco sutil */
}

/* Estilos de los días */
.day-pending { 
    background: var(--card);
    border: 1px dashed var(--line);
    /* === CAMBIO AQUÍ: color de texto más claro === */
    color: var(--muted); /* El texto principal (Día 3) será --muted */
    opacity: 0.7;
}
.day-pending .day-number {
    color: var(--muted); /* El texto "Día" también será --muted */
    opacity: 0.7;
}

.day-current { 
    background: var(--panel);
    border: 2px solid var(--brand-2); 
    color: var(--brand-2); /* El texto (Día 2) será cian */
}
.day-current .day-number {
    color: var(--brand-2); /* El texto "Día" también será cian */
}

.day-complete { 
    background: var(--brand); 
    border: 2px solid var(--brand);
    color: var(--bg); /* El texto (Día 1) será oscuro */
}
.day-complete .day-number {
     color: var(--bg); /* El texto "Día" también será oscuro */
     opacity: 0.7;
}

/* (Asegúrate de tener .guia-grid y .guia-card definidos en main.css) */
</style>
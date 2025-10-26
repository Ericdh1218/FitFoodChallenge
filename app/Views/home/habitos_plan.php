<?php
/** @var array $area */
/** @var array $planUsuario */
/** @var int $pasoActual */
/** @var array $contenido */
/** @var array $recetas */ // Recetas sugeridas (solo para tipo 'recetas')
?>

<a class="link" href="<?= url('/habitos') ?>">← Volver a elegir Hábito</a>

<?php 
$mensajeQuiz = null;
if (isset($_SESSION['flash_message_quiz'])) {
    $mensajeQuiz = $_SESSION['flash_message_quiz'];
    unset($_SESSION['flash_message_quiz']); 
}
?>
<?php if ($mensajeQuiz): ?>
    <div class="flash-info card" style="margin-top: 16px; background: var(--brand-2); color: var(--bg);">
        <?= htmlspecialchars($mensajeQuiz) ?>
    </div>
<?php endif; ?>
<div class="plan-pasos-container section">
    
    <div class="plan-header">
        <span class="area-icono" style="font-size: 2rem;"><?= htmlspecialchars($area['icono'] ?? '🎯') ?></span>
        <h1 style="margin: 8px 0;"><?= htmlspecialchars($area['titulo']) ?></h1>
        <p class="muted">Estás en el Paso <?= $pasoActual ?> de 3</p>
    </div>

    <div class="paso-contenido">
        <h2><?= htmlspecialchars($contenido['titulo_paso']) ?></h2>
        <p><?= nl2br(htmlspecialchars($contenido['texto_intro'])) ?></p>

        <?php if ($contenido['tipo'] === 'quiz' && isset($contenido['quiz'])): ?>
            
            <form id="planForm" action="<?= url('/habitos/advance-step') ?>" method="POST" style="margin-top: 24px;">
                 <h4><?= htmlspecialchars($contenido['quiz']['pregunta']) ?></h4>
                 <div class="quiz-opciones">
                     <?php foreach ($contenido['quiz']['opciones'] as $key => $opcion): ?>
                        <div class="quiz-opcion">
                             <input type="radio" name="respuesta_quiz" id="opcion_<?= $key ?>" value="<?= $key ?>" required>
                             <label for="opcion_<?= $key ?>"><?= htmlspecialchars($opcion) ?></label>
                        </div>
                     <?php endforeach; ?>
                 </div>
                 
                 <?php elseif ($contenido['tipo'] === 'recetas' && !empty($recetas)): ?>
            
            <form id="planForm" action="<?= url('/habitos/advance-step') ?>" method="POST" style="margin-top: 24px;">
                 <div class="grid cards" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px;">
                     <?php foreach ($recetas as $receta): ?>
                         <?php // Lógica para imagen de receta (simplificada)
                            $imgSrc = null;
                            if (!empty($receta['imagen'])) {
                                if (preg_match('~^https?://~', $receta['imagen'])) { $imgSrc = $receta['imagen']; }
                                elseif (isset($receta['user_id']) && $receta['user_id'] !== null && strpos($receta['imagen'], '_') !== false) { $imgSrc = url('assets/img/recetas_usuario/' . $receta['imagen']); }
                                else { $imgSrc = url('assets/img/recetas/' . $receta['imagen']); }
                            }
                         ?>
                         <a href="<?= url('/receta?id=' . $receta['id']) ?>" class="guia-card">
                             <?php if ($imgSrc): ?>
                                <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($receta['titulo']) ?>">
                             <?php endif; ?>
                             <div class="guia-card-content">
                                <h4><?= htmlspecialchars($receta['titulo']) ?></h4>
                             </div>
                         </a>
                     <?php endforeach; ?>
                 </div>
                 
                 <?php elseif ($contenido['tipo'] === 'reflexion' && isset($contenido['opciones_reflexion'])): ?>
            
            <form id="planForm" action="<?= url('/habitos/advance-step') ?>" method="POST" style="margin-top: 24px;">
                 <div class="reflexion-opciones">
                     <?php foreach ($contenido['opciones_reflexion'] as $key => $texto): ?>
                         <button type="submit" name="reflexion" value="<?= $key ?>" class="btn ghost btn-reflexion">
                             <?= htmlspecialchars($texto) ?>
                         </button>
                     <?php endforeach; ?>
                 </div>
                 <?php else: // Tipo 'info' o por defecto ?>
            
             <form id="planForm" action="<?= url('/habitos/advance-step') ?>" method="POST" style="margin-top: 24px;">
                 <?php endif; ?>

        <input type="hidden" name="plan_id" value="<?= $planUsuario['id'] ?>">
            <input type="hidden" name="paso_actual" value="<?= $pasoActual ?>">
            <input type="hidden" name="area_codigo" value="<?= $area['codigo'] ?>">
            
            <?php // Muestra el botón de submit solo si no es el paso de reflexión con botones
            if ($contenido['tipo'] !== 'reflexion'): 
            ?>
                <button type="submit" class="btn primary btn-avanzar">
                    <?= htmlspecialchars($contenido['boton']) ?>
                </button>
            <?php endif; ?>
        </form>
        </div> </div> <style>
/* Estilos para Quiz */
.quiz-opciones { margin-top: 16px; }
.quiz-opcion { margin-bottom: 10px; display: flex; align-items: center; }
.quiz-opcion input[type="radio"] { margin-right: 10px; width: 18px; height: 18px; accent-color: var(--brand); }
.quiz-opcion label { font-size: 1rem; cursor: pointer; }

/* Estilos para Tarjetas de Receta (reusa .guia-card) */
/* .guia-card { ... } (ya deberías tenerlo) */
/* .guia-card img { ... } */
/* .guia-card-content { ... } */
/* .guia-card h4 { ... } */

/* Estilos para Botones de Reflexión */
.reflexion-opciones { display: grid; gap: 10px; margin-top: 16px; }
.btn-reflexion { width: 100%; text-align: center; justify-content: center; } 
.flash-info { padding: 12px 16px; font-weight: 500; text-align: center; }
</style>
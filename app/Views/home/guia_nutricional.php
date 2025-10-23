<?php 
/** @var array $ejemplos */ 
/** @var array $usuario */ // Necesitamos la variable $usuario aquí
/** @var array|null $estimacion */ // Y la variable $estimacion también
?>

<h1 style="margin-bottom: 8px;">Guía Nutricional Orientativa</h1>
<p class="muted">Ideas y ejemplos para estructurar tus comidas diarias.</p>

<div class="card" style="border-left: 4px solid var(--brand-2); margin-top: 24px;">
    <h3 style="margin-top: 0; color: var(--brand-2);">¡Importante!</h3>
    <p class="muted" style="line-height: 1.7;">
        Esta guía es solo una herramienta de orientación y no sustituye la consulta con un nutriólogo o profesional de la salud. 
        Las porciones y necesidades calóricas varían para cada persona.
    </p>
</div>


<section class="section" style="margin-top: 32px;">
    <h2>Tu Estimación Diaria</h2>
    
    <?php if ($estimacion): ?>
        <p class="muted">Basado en tus datos, esta es una estimación de tus necesidades diarias. ¡Recuerda que es solo una guía!</p>
        <div class="estimacion-grid">
            <div class="stat">
                <strong><?= number_format($estimacion['calorias']) ?></strong>
                <span>Calorías (kcal)</span>
            </div>
            <div class="stat">
                <strong><?= $estimacion['proteinas'] ?> g</strong>
                <span>Proteínas</span>
            </div>
            <div class="stat">
                <strong><?= $estimacion['carbos'] ?> g</strong>
                <span>Carbohidratos</span>
            </div>
             <div class="stat">
                <strong><?= $estimacion['grasas'] ?> g</strong>
                <span>Grasas</span>
            </div>
        </div>
    <?php else: ?>
        <p class="muted">No pudimos calcular tu estimación. Asegúrate de haber completado tu <a href="<?= url('/micuenta') ?>" class="link">perfil</a> con peso, altura, edad, género y nivel de actividad.</p>
    <?php endif; ?>
</section>
<div class="guia-secciones" style="margin-top: 32px;">

    <?php foreach ($ejemplos as $categoria => $recetas): ?>
        <?php if (!empty($recetas)): ?>
            
            <section class="section">
                <h2>Ideas: <?= htmlspecialchars(ucfirst($categoria)) ?></h2>
                <div class="guia-grid">
                    
                    <?php foreach ($recetas as $receta): ?>
                        <?php
                        $img = $receta['imagen'];
                        if ($img && !preg_match('~^https?://~', $img)) {
                            $img = url('assets/img/' . $img);
                        }
                        $detalleUrl = url('/receta?id=' . $receta['id']);
                        ?>
                        
                        <a href="<?= htmlspecialchars($detalleUrl) ?>" class="guia-card">
                            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($receta['titulo']) ?>">
                            <div class="guia-card-content">
                                <h4><?= htmlspecialchars($receta['titulo']) ?></h4>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

        <?php endif; ?>
    <?php endforeach; ?>
    </div>
<?php
/** @var array $area */
/** @var array $ejercicios */
/** @var array $miniTest */
?>

<a class="link" href="<?= url('/habitos') ?>">← Volver a Hábitos</a>
<div class="plan-header" style="text-align: center; margin: 16px 0 32px 0;">
    <span class="area-icono" style="font-size: 2.5rem;"><?= htmlspecialchars($area['icono'] ?? '🎯') ?></span>
    <h1 style="margin: 8px 0;"><?= htmlspecialchars($area['titulo']) ?></h1>
    <p class="muted"><?= htmlspecialchars($area['descripcion_corta']) ?></p>
</div>

<div class="layout-sidebar-wrapper">

    <main class="layout-content">
        
        <section class="section" style="margin-bottom: 24px;">
            <h2>🕺 ¡Actívate!</h2>
            <p class="muted">Moverse es clave para la salud mental y física. No necesitas un gimnasio, ¡solo tu cuerpo y 5 minutos!</p>
            
            <div class="video-container" style="aspect-ratio: 16/9; margin-top: 16px;">
                <iframe src="https://www.youtube.com/embed/gC_L9qAHVJ8" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                </iframe>
            </div>
        </section>

        <section class="section">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <h2>⏱️ Ideas de 5 Minutos (Sin Equipo)</h2>
                <button id="btnRefrescarEjercicios" class="btn ghost btn-sm" style="margin: 0;">
                    Ver otros &rarr;
                </button>
            </div>
            <p class="muted">Aquí tienes algunos ejemplos de nuestra biblioteca. Haz clic para ver la técnica:</p>
            
            <div id="guiaGridEjercicios" class="guia-grid" style="margin-top: 16px; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
                
                <?php 
                // Carga inicial usando la vista parcial (¡DEBE USAR partial()!)
                if (isset($ejercicios) && !empty($ejercicios)) {
                    // Esta función 'partial' la creamos antes
                    partial('home._partial_ejercicio_cards', ['ejercicios' => $ejercicios]);
                } else {
                    echo '<p class="muted" style="grid-column: 1 / -1; text-align: center;">No se encontraron ejercicios "Sin Equipo".</p>';
                }
                ?>
                
            </div>
        </section>

    </main>
    
    <aside class="layout-sidebar">

        <section class="section" style="margin-bottom: 24px;">
            <h2>💡 Mini-Test</h2>
            <p class="muted"><?= htmlspecialchars($miniTest['pregunta']) ?></p>
            
            <div class="minitest-opciones" id="miniTestContainer">
                <?php foreach ($miniTest['opciones'] as $key => $opcion): ?>
                    <button class="btn ghost minitest-btn" data-tipo="<?= htmlspecialchars($opcion['tipo']) ?>">
                        <?= htmlspecialchars($opcion['nombre']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <div id="miniTestFeedback" class="minitest-feedback card" style="display:none; margin-top: 16px;">
                <h4 id="miniTestFeedbackTitulo"></h4>
                <p id="miniTestFeedbackTexto" class="muted"></p>
                <a href="#" id="miniTestEnlace" class="link" style="margin-top: 10px;">Ver ejercicios recomendados &rarr;</a>
            </div>
        </section>
        
        <section class="section" style="text-align: center;">
            <h2>🎯 Reto: 1 Semana Moviéndote</h2>
            <p class="reto-texto">Tu misión: acumular al menos 15 minutos de actividad (como los ejercicios de ejemplo) cada día. ¡Sube las escaleras, baila, camina! Todo suma.</p>
            <a href="<?= url('/progreso') ?>" class="btn primary">Ir a mi Check-in ✅</a>
        </section>

    </aside>

</div>
<style>
/* ... (Tu bloque <style> se queda igual) ... */
.minitest-opciones { display: grid; grid-template-columns: 1fr; gap: 10px; margin-top: 16px; }
.minitest-btn { width: 100%; justify-content: center; padding: 14px 18px; color: var(--text) !important; }
.minitest-feedback { padding: 16px; }
.minitest-feedback h4 { margin: 0 0 8px 0; color: var(--brand); }
.guia-grid { display: grid; gap: 16px; }
.guia-card { display: block; background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-sm); overflow: hidden; transition: all 0.2s ease; }
.guia-card:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
.guia-card img { width: 100%; height: 100px; object-fit: cover; }
.guia-card-content { padding: 12px; }
.guia-card h4 { margin: 0 0 4px 0; color: var(--text); font-size: 0.9rem; line-height: 1.3; }
.guia-card small.muted { font-size: 0.8rem; }
.reto-texto { color: var(--text); } 
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Lógica del Mini-Test ---
    const testContainer = document.getElementById('miniTestContainer');
    if (testContainer) { 
        const feedbackDiv = document.getElementById('miniTestFeedback');
        const feedbackTitulo = document.getElementById('miniTestFeedbackTitulo');
        const feedbackTexto = document.getElementById('miniTestFeedbackTexto');
        const feedbackEnlace = document.getElementById('miniTestEnlace');
        
        const tiposEjercicio = {
            'Cardio': {
                titulo: '¡Genial! Eres de Cardio',
                texto: 'Te gusta sentir el corazón latir. Busca actividades como correr, saltar la cuerda o rutinas HIIT.',
                enlace: '<?= url("/deportes?tipo_entrenamiento=Cardio") ?>'
            },
            'Fuerza': {
                titulo: '¡Excelente! Eres de Fuerza',
                texto: 'Te gusta sentir el trabajo muscular. Busca rutinas de fuerza con peso corporal o equipo ligero.',
                enlace: '<?= url("/deportes?tipo_entrenamiento=Fuerza&equipamiento=Sin Equipo") ?>'
            }
        };

        testContainer.addEventListener('click', function(event) {
            if (!event.target.matches('.minitest-btn')) { return; }
            
            const boton = event.target;
            const tipo = boton.dataset.tipo; 
            const data = tiposEjercicio[tipo] || tiposEjercicio['Cardio']; 

            testContainer.querySelectorAll('.minitest-btn').forEach(btn => {
                btn.style.borderColor = 'var(--line)';
                btn.style.borderWidth = '1px';
            });
            boton.style.borderColor = 'var(--brand)';
            boton.style.borderWidth = '2px';

            feedbackTitulo.textContent = data.titulo;
            feedbackTexto.textContent = data.texto;
            feedbackEnlace.href = data.enlace;
            feedbackDiv.style.display = 'block'; 
        });
    } // Fin if (testContainer)

    // --- Lógica para Refrescar Ejercicios ---
    const btnRefrescar = document.getElementById('btnRefrescarEjercicios');
    const gridContainer = document.getElementById('guiaGridEjercicios');

    if (btnRefrescar && gridContainer) {
        btnRefrescar.addEventListener('click', async function() {
            gridContainer.innerHTML = '<p class="muted" style="text-align: center; grid-column: 1 / -1;">Buscando más ejercicios...</p>';
            btnRefrescar.disabled = true; 

            try {
                // Ajusta el límite (ej. 3 ejercicios)
                const response = await fetch('<?= url('/ejercicios/aleatorios?equipamiento=Sin Equipo&limit=3') ?>'); 
                if (!response.ok) throw new Error('Error de red');

                const html = await response.text(); 
                gridContainer.innerHTML = html;
                
            } catch (error) {
                console.error('Error al refrescar ejercicios:', error);
                gridContainer.innerHTML = '<p class="muted" style="color:var(--danger); grid-column: 1 / -1;">Error al cargar. Intenta de nuevo.</p>';
            } finally {
                btnRefrescar.disabled = false; 
            }
        });
    } // Fin if (btnRefrescar)
}); // Fin DOMContentLoaded
</script>
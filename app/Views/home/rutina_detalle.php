<?php
/** @var array $rutina */
/** @var array $ejercicios */
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<article>
    <a class="link" href="<?= url('/rutinas') ?>">← Volver a Rutinas</a>

    <h1 style="margin-top:8px"><?= htmlspecialchars($rutina['nombre_rutina']) ?></h1>
    <p class="muted"><?= htmlspecialchars($rutina['descripcion']) ?></p>
    <span class="tag">Nivel: <?= htmlspecialchars($rutina['nivel']) ?></span>

    <div class="section cronometro-container" style="margin-top: 24px; text-align: center; border-color: var(--brand-2);">
        <h2>¡Inicia tu Rutina!</h2>
        
        <div id="cronometroDisplay" class="cronometro-display">
            00:00
        </div>
        
        <div class="cronometro-controles" style="display: flex; gap: 10px; justify-content: center; margin-top: 16px;">
            <button id="btnCronometroStart" class="btn primary">Iniciar</button>
            <button id="btnCronometroStop" class="btn ghost" style="display: none; background-color: var(--danger); color: white; border: none;">Terminar y Guardar</button>
        </div>
        
        <form id="formCompletarRutina" style="display: none;">
            <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">
        </form>
    </div>
    <h2 style="margin-top: 32px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">
        Ejercicios de la Rutina
    </h2>

    <div class="ejercicio-lista">
        <?php foreach ($ejercicios as $ejercicio): ?>
            <div class="ejercicio-item card">
                <div>
                    <h4><?= htmlspecialchars($ejercicio['nombre']) ?></h4>
                    <p class="muted">
                        <strong>Series/Reps:</strong> <?= htmlspecialchars($ejercicio['series_reps']) ?>
                    </p>
                </div>
                <?php
                    // 1. Codifica la URL actual de la rutina prediseñada para pasarla
                    $returnUrlRutina = urlencode(url('/rutina?id=' . $rutina['id'])); 
                ?>
                <a class="btn ghost" href="<?= url('/deportes?id=' . $ejercicio['id'] . '&return_url=' . $returnUrlRutina) ?>"> 
                    Ver Ejercicio
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</article>

<style>
.cronometro-display {
    font-size: 3.5rem;
    font-weight: 700;
    color: var(--text);
    margin: 10px 0;
    font-family: 'Poppins', sans-serif;
    letter-spacing: 1.5px;
}
.cronometro-controles .btn {
    min-width: 120px;
    margin-top: 0; /* Sobrescribe el margen de .btn */
    margin-right: 0; /* Sobrescribe el margen de .btn */
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const display = document.getElementById('cronometroDisplay');
    const btnStart = document.getElementById('btnCronometroStart');
    const btnStop = document.getElementById('btnCronometroStop');
    const formCompletar = document.getElementById('formCompletarRutina');
    
    // --- NUEVA CONSTANTE: Tiempo mínimo en segundos ---
    const MINUTOS_MINIMOS = 5;
    const SEGUNDOS_MINIMOS = MINUTOS_MINIMOS * 60; 
    // const SEGUNDOS_MINIMOS = 10; // <-- Usa esta línea para pruebas rápidas (10 segundos)
    // --------------------------------------------------
    
    let timerInterval = null;
    let segundosPasados = 0;
    
    function formatearTiempo(seg) {
        const min = Math.floor(seg / 60);
        const sec = seg % 60;
        return `${min.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
    }

    // --- Iniciar Cronómetro ---
    btnStart.addEventListener('click', function() {
        btnStart.style.display = 'none';
        btnStop.style.display = 'inline-flex';
        
        segundosPasados = 0; 
        display.textContent = '00:00';

        timerInterval = setInterval(() => {
            segundosPasados++;
            display.textContent = formatearTiempo(segundosPasados);
            
            // Opcional: Cambiar color del botón cuando se alcanza el mínimo
            if (segundosPasados === SEGUNDOS_MINIMOS) {
                btnStop.style.backgroundColor = 'var(--brand)'; // Verde
                btnStop.style.color = 'var(--bg, #0b1220)';
                btnStop.textContent = 'Terminar (¡Puntos listos!)';
            }
        }, 1000);
    });

    // --- Detener/Completar Cronómetro (LÓGICA MODIFICADA) ---
    btnStop.addEventListener('click', async function() {
        
        clearInterval(timerInterval); // Detiene el contador
        
        // --- REVISIÓN DE TIEMPO MÍNIMO ---
        if (segundosPasados < SEGUNDOS_MINIMOS) {
            // No ha cumplido el tiempo, mostrar advertencia
            const result = await Swal.fire({
                title: '¿Seguro?',
                text: `Has entrenado menos de ${MINUTOS_MINIMOS} minutos. Si terminas ahora, no ganarás los puntos de XP.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, terminar (sin puntos)',
                cancelButtonText: 'Seguir entrenando',
                background: 'var(--panel)',
                color: 'var(--text)'
            });

            if (result.isConfirmed) {
                // El usuario decidió terminar sin puntos. Solo redirigir.
                window.location.href = '<?= url('/rutinas') ?>'; // Vuelve a la lista de rutinas
            } else {
                // El usuario quiere seguir. Reanuda el cronómetro.
                timerInterval = setInterval(() => {
                    segundosPasados++;
                    display.textContent = formatearTiempo(segundosPasados);
                }, 1000);
            }
            return; // No continuar con el guardado de puntos
        }
        // --- FIN REVISIÓN DE TIEMPO ---

        // Si llegó aquí, SÍ cumplió el tiempo mínimo
        btnStop.disabled = true;
        btnStop.textContent = 'Guardando...';

        const formData = new FormData(formCompletar);
        // formData.append('duracion_segundos', segundosPasados); // Opcional

        try {
            const response = await fetch('<?= url('/rutinas/completar') ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    title: '¡Rutina Completada!',
                    text: data.message, // "¡Ganaste +30 XP!"
                    icon: 'success',
                    background: 'var(--panel)',
                    color: 'var(--text)'
                }).then(() => {
                    window.location.href = '<?= url('/rutinas') ?>';
                });
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            Swal.fire({
                 title: 'Error',
                 text: 'No se pudo guardar tu progreso: ' + (error.message || 'Error de conexión'), 
                 icon: 'error',
                 background: 'var(--panel)',
                 color: 'var(--text)'
            });
            btnStop.disabled = false; // Permite reintentar
            btnStop.textContent = 'Terminar y Guardar';
        }
    });
});
</script>
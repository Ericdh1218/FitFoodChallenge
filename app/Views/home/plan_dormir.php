<?php
/** @var array $area */
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
            <h2>🌙 La Ciencia de Dormir Bien</h2>
            <p class="muted">Dormir no es perder el tiempo, es recargar tu cerebro y cuerpo. Mira por qué.</p>

            <div class="video-container" style="aspect-ratio: 16/9; margin-top: 16px;">
    <iframe src="https://www.youtube.com/embed/3fWP9vHWGxU" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
            allowfullscreen>
    </iframe>
</div>
        </section>

        <section class="section">
            <h2>Consejos de Higiene del Sueño</h2>
            <ul class="lista-consejos-sueno">
                <li><strong>Consistencia:</strong> Intenta acostarte y levantarte a la misma hora, incluso los fines de
                    semana.</li>
                <li><strong>Sin pantallas:</strong> Evita el celular, tablet o TV al menos 30-60 minutos antes de
                    dormir. La luz azul engaña a tu cerebro.</li>
                <li><strong>Ambiente:</strong> Mantén tu habitación oscura, silenciosa y fresca.</li>
                <li><strong>Cena ligera:</strong> Evita comidas pesadas o mucho líquido justo antes de acostarte.</li>
                <li><strong>Evita la cafeína:</strong> No tomes café, refrescos de cola o bebidas energéticas por la
                    tarde/noche.</li>
            </ul>
        </section>

    </main>

    <aside class="layout-sidebar">

        <section class="section" style="margin-bottom: 24px;">
            <h2>⏰ Simulador del Sueño</h2>
            <p class="muted">¿Cuántas horas dormiste anoche? Selecciona una opción:</p>

            <div class="simulador-opciones-sueno" id="simuladorSuenoContainer">
                <button class="btn ghost simulador-btn-sueno" data-horas="4-5">4-5 horas</button>
                <button class="btn ghost simulador-btn-sueno" data-horas="6">6 horas</button>
                <button class="btn ghost simulador-btn-sueno" data-horas="7-8">7-8 horas</button>
            </div>

            <div id="simuladorSuenoFeedback" class="simulador-feedback card" style="display:none; margin-top: 16px;">
                <h4 id="simuladorSuenoTitulo"></h4>
                <p id="simuladorSuenoTexto" class="muted"></p>
            </div>
        </section>

        <section class="section" style="text-align: center;">
            <h2>🎯 Tu Reto</h2>
            <p class="reto-texto">Intenta dormir 7-8 horas esta noche. ¡Registra tu progreso mañana!</p>
            <a href="<?= url('/progreso') ?>" class="btn primary">Ir a mi Check-in ✅</a>
        </section>

    </aside>

</div>

<style>
    /* Estilos para Consejos de Sueño */
    .lista-consejos-sueno {
        list-style-type: '🌙';
        /* Emoji como viñeta */
        padding-left: 20px;
    }

    .lista-consejos-sueno li {
        padding-left: 10px;
        margin-bottom: 12px;
        line-height: 1.6;
        color: var(--text);
    }

    /* Estilos para el Simulador de Sueño */
    .simulador-opciones-sueno {
        display: grid;
        grid-template-columns: 1fr;
        /* Una columna */
        gap: 10px;
        margin-top: 16px;
    }

    .simulador-btn-sueno {
        width: 100%;
        justify-content: center;
        padding: 14px 18px;
        color: var(--text) !important;
        /* Texto claro */
    }

    .simulador-feedback {
        padding: 16px;
    }

    .simulador-feedback h4 {
        margin: 0 0 8px 0;
    }

    .simulador-feedback.horas-pocas h4 {
        color: var(--danger);
    }

    /* Rojo */
    .simulador-feedback.horas-ok h4 {
        color: #f59e0b;
    }

    /* Naranja */
    .simulador-feedback.horas-ideal h4 {
        color: var(--brand);
    }

    /* Verde */

    /* Estilo para el texto del Reto (ya deberías tenerlo) */
    .reto-texto {
        color: var(--text);
        font-size: 1rem;
        line-height: 1.6;
        margin: 16px 0;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Lógica del Simulador de Sueño ---
        const suenoContainer = document.getElementById('simuladorSuenoContainer');
        if (suenoContainer) { // Asegura que el script solo corra si los elementos existen
            const feedbackDiv = document.getElementById('simuladorSuenoFeedback');
            const feedbackTitulo = document.getElementById('simuladorSuenoTitulo');
            const feedbackTexto = document.getElementById('simuladorSuenoTexto');

            const impactosSueno = {
                '4-5': {
                    titulo: '📉 ¡Alerta! Modo Supervivencia',
                    texto: 'Dormir tan poco afecta gravemente tu concentración, memoria y estado de ánimo. Es probable que tengas más antojos de azúcar y comida chatarra.',
                    clase: 'horas-pocas'
                },
                '6': {
                    titulo: '🤔 Aceptable, pero no ideal',
                    texto: 'Con 6 horas puedes funcionar, pero tu rendimiento cognitivo y físico no está al 100%. A largo plazo, sigue siendo insuficiente para una recuperación completa.',
                    clase: 'horas-ok'
                },
                '7-8': {
                    titulo: '🚀 ¡Ideal! Recuperación Completa',
                    texto: '¡Felicidades! Este es el rango óptimo. Tu cuerpo y mente se reparan, consolidas la memoria y tendrás energía estable durante el día.',
                    clase: 'horas-ideal'
                }
            };

            suenoContainer.addEventListener('click', function (event) {
                if (!event.target.matches('.simulador-btn-sueno')) { return; }

                const boton = event.target;
                const horas = boton.dataset.horas;
                const data = impactosSueno[horas];

                // Resetea y aplica estilos a botones
                suenoContainer.querySelectorAll('.simulador-btn-sueno').forEach(btn => {
                    btn.style.borderColor = 'var(--line)';
                    btn.style.borderWidth = '1px';
                });
                boton.style.borderColor = data.clase === 'horas-ideal' ? 'var(--brand)' : (data.clase === 'horas-ok' ? '#f59e0b' : 'var(--danger)');
                boton.style.borderWidth = '2px';

                // Actualizar contenido del feedback
                feedbackTitulo.textContent = data.titulo;
                feedbackTexto.textContent = data.texto;
                feedbackDiv.className = 'simulador-feedback card ' + data.clase; // Resetea y añade clase
                feedbackDiv.style.display = 'block'; // Mostrar
            });
        }
    });
</script>
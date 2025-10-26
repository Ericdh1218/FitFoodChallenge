<?php
/** @var array $area */
/** @var array $trivia */
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
            <h2>🎥 La Importancia de las Frutas y Verduras</h2>
            <p class="muted">Un vistazo rápido a por qué son tan esenciales para tu energía y salud diaria.</p>
            
            <div class="video-container" style="aspect-ratio: 16/9; margin-top: 16px;">
                <iframe src="https://www.youtube.com/embed/ZRItmC9ODXE" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                </iframe>
            </div>
        </section>

    </main>
    
    <aside class="layout-sidebar">

        <section class="section" style="margin-bottom: 24px;">
            <h2>🧠 Trivia Rápida</h2>
            <p id="triviaPreguntaTexto" class="trivia-pregunta"><?= htmlspecialchars($trivia['pregunta']) ?></p>
            
            <div class="trivia-opciones" id="triviaContainer">
                <?php foreach ($trivia['opciones'] as $key => $opcion): ?>
                    <button class="btn ghost trivia-btn" data-answer="<?= $key ?>">
                        <?= htmlspecialchars($opcion) ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <div id="triviaFeedback" class="trivia-feedback card" style="display:none; margin-top: 16px;">
                <h4 id="triviaFeedbackTitulo"></h4>
                <p id="triviaFeedbackTexto" class="muted"></p>
            </div>
            
            <button id="triviaBtnSiguiente" class="btn primary" style="display: none;">
                Siguiente Pregunta &rarr;
            </button>
        </section>
        
        <section class="section" style="text-align: center;">
            <h2>🎯 Tu Reto</h2>
            <p class="reto-texto">Intenta que la mitad de tu plato en el almuerzo y la cena sean verduras hoy. ¡Registra tu progreso!</p>
            <a href="<?= url('/progreso') ?>" class="btn primary">Ir a mi Check-in ✅</a>
        </section>

    </aside>

</div>

<style>
/* Estilos para la Trivia */
.trivia-opciones { display: grid; grid-template-columns: 1fr; gap: 10px; margin-top: 16px; }
@media (min-width: 600px) { .trivia-opciones { grid-template-columns: 1fr 1fr; } }
.trivia-btn { width: 100%; justify-content: center; padding: 14px 18px; }
.trivia-feedback { padding: 16px; }
.trivia-feedback h4 { margin: 0 0 8px 0; }
.trivia-feedback.correcta h4 { color: var(--brand); }
.trivia-feedback.incorrecta h4 { color: var(--danger); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Referencias a elementos del DOM ---
    const triviaContainer = document.getElementById('triviaContainer');
    const feedbackDiv = document.getElementById('triviaFeedback');
    const feedbackTitulo = document.getElementById('triviaFeedbackTitulo');
    const feedbackTexto = document.getElementById('triviaFeedbackTexto');
    const preguntaTexto = document.getElementById('triviaPreguntaTexto');
    const btnSiguiente = document.getElementById('triviaBtnSiguiente');
    
    // Carga inicial de datos de la trivia (pasados desde PHP)
    let triviaData = <?= json_encode($trivia, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    
    /**
     * Función para mostrar (renderizar) una pregunta y sus opciones
     */
    function renderTrivia(data) {
        if (!data || !data.opciones) {
            preguntaTexto.textContent = '¡Felicidades, completaste todas las preguntas por hoy!';
            triviaContainer.innerHTML = '';
            feedbackDiv.style.display = 'none';
            btnSiguiente.style.display = 'none';
            return;
        }
        
        // Actualiza los datos globales
        triviaData = data; 
        
        // Actualiza el texto de la pregunta
        preguntaTexto.textContent = data.pregunta;
        
        // Limpia botones anteriores y feedback
        triviaContainer.innerHTML = '';
        feedbackDiv.style.display = 'none';
        btnSiguiente.style.display = 'none';

        // Crea los nuevos botones de opción
        for (const key in data.opciones) {
            if (data.opciones.hasOwnProperty(key)) {
                const opcion = data.opciones[key];
                const boton = document.createElement('button');
                boton.className = 'btn ghost trivia-btn'; // Clases base
                boton.dataset.answer = key; // Guarda la clave (a, b, c...)
                boton.textContent = opcion; // El texto de la opción
                triviaContainer.appendChild(boton);
            }
        }
    }

    /**
     * Listener para cuando se responde una pregunta
     */
    triviaContainer.addEventListener('click', function(event) {
        // Asegurarse que el clic fue en un botón de trivia
        if (!event.target.matches('.trivia-btn')) { return; }
        
        // Evitar doble clic
        if (triviaContainer.dataset.answered === 'true') return; 
        triviaContainer.dataset.answered = 'true'; // Marcar como respondido

        const boton = event.target;
        const respuestaUsuario = boton.dataset.answer;
        const respuestaCorrecta = triviaData.correcta;
        const feedbackCorrecto = triviaData.feedback_correcta;
        const feedbackIncorrecto = triviaData.feedback_incorrecta;

        // Deshabilita botones y aplica clases de estilo
        triviaContainer.querySelectorAll('.trivia-btn').forEach(btn => {
            btn.disabled = true;
            if (btn.dataset.answer === respuestaCorrecta) {
                btn.classList.add('correcta-marcada');
            } else if (btn.dataset.answer === respuestaUsuario) {
                btn.classList.add('incorrecta-marcada');
            }
        });

        // Muestra Feedback
        if (respuestaUsuario === respuestaCorrecta) {
            feedbackTitulo.textContent = '¡Correcto!';
            feedbackDiv.className = 'trivia-feedback card correcta';
            feedbackTexto.textContent = feedbackCorrecto;
        } else {
            feedbackTitulo.textContent = '¡Casi!';
            feedbackDiv.className = 'trivia-feedback card incorrecta';
            feedbackTexto.textContent = feedbackIncorrecto;
        }
        feedbackDiv.style.display = 'block';
        btnSiguiente.style.display = 'inline-flex'; // Muestra el botón "Siguiente"
    });

    /**
     * Listener para el botón "Siguiente Pregunta"
     */
    btnSiguiente.addEventListener('click', async function() {
        // Oculta feedback y botón, muestra "cargando"
        feedbackDiv.style.display = 'none';
        btnSiguiente.style.display = 'none';
        preguntaTexto.textContent = 'Cargando nueva pregunta...';
        triviaContainer.innerHTML = '<p class="muted">...</p>';
        triviaContainer.dataset.answered = 'false'; // Resetea el estado de respuesta

        try {
            // Llama al endpoint AJAX (puedes cambiar 'Nutrición' por otra categoría)
            const response = await fetch('<?= url('/trivia/pregunta-aleatoria') ?>?categoria=Nutrición');
            if (!response.ok) throw new Error('Error de red');
            
            const newData = await response.json();
            
            if (newData.success && newData.pregunta) {
                renderTrivia(newData.pregunta); // Renderiza la nueva pregunta
            } else {
                preguntaTexto.textContent = 'Error al cargar la pregunta.';
                console.error('Respuesta AJAX no exitosa:', newData.message);
            }
        } catch (error) {
            console.error('Error fetching new question:', error);
            preguntaTexto.textContent = 'Error de conexión al cargar trivia.';
        }
    });

    // --- Renderiza la primera pregunta (la que cargó PHP) ---
    renderTrivia(triviaData);
});
</script>
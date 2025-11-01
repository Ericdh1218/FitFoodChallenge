<?php /** @var string $preguntasJson */ ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <div class="quiz-container section">
    <h1>Quiz del Glosario</h1>
    
    <div class="quiz-progreso">
        Pregunta <span id="quizNumActual">1</span> de <span id="quizNumTotal">?</span>
        <div class="progress-bar-container" style="height: 10px; margin-top: 8px;">
            <div id="quizProgressBar" class="progress-bar-fill" style="width: 0%;"></div>
        </div>
    </div>
    
    <hr style="border: 0; border-top: 1px solid var(--line); margin: 20px 0;">
    
    <div id="quizPreguntaContainer">
        <h2 id="quizPreguntaTexto">Cargando pregunta...</h2>
        <div id="quizOpciones" class="quiz-opciones-lista">
            </div>
    </div>
    
    <button id="quizBtnSiguiente" class="btn primary" style="width: 100%; margin-top: 24px;" disabled>
        Selecciona una opción
    </button>
</div>

<style>
.quiz-container { max-width: 800px; margin: 24px auto; }
.quiz-progreso { color: var(--muted); }
.quiz-opciones-lista { display: grid; grid-template-columns: 1fr; gap: 12px; margin-top: 20px; }
.quiz-opcion {
    display: block;
    width: 100%;
    padding: 16px;
    font-size: 0.95rem;
    line-height: 1.5;
    text-align: left;
    border: 2px solid var(--line);
    background: var(--card);
    color: var(--text);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: border-color 0.2s ease, background 0.2s ease;
}
.quiz-opcion:hover { background: var(--panel); border-color: var(--brand-2); }
.quiz-opcion.selected { border-color: var(--brand); background: #064e3b; } /* Verde seleccionado */
.quiz-opcion.correcta { border-color: var(--brand); background: #064e3b; }
.quiz-opcion.incorrecta { border-color: var(--danger); background: #450a0a; }
.quiz-opcion {
  display: block;
  width: 100%;
  padding: 16px;
  font-size: 0.95rem;
  text-align: left;
  border: 2px solid var(--line);
  background: var(--card);
  color: var(--text);
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all 0.2s ease;
}
.quiz-opcion:hover { background: var(--panel); border-color: var(--brand-2); }

.quiz-opcion .quiz-ico {
  float: right;
  font-weight: 900;
  opacity: .9;
}

.quiz-opcion.correcta { border-color: var(--brand); background: #064e3b; }   /* Verde */
.quiz-opcion.incorrecta { border-color: var(--danger); background: #450a0a; } /* Rojo */

/* 🔥 Efecto de vibración (shake) */
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  20%, 60% { transform: translateX(-6px); }
  40%, 80% { transform: translateX(6px); }
}
.quiz-opcion.shake {
  animation: shake 0.35s ease;
}
</style>

<style>
/* Opciones ya existen; agregamos iconito a la derecha */
.quiz-opcion .quiz-ico { float: right; font-weight: 900; opacity: .9; }
.quiz-opcion.correcta { border-color: var(--brand); background: #064e3b; }   /* verde */
.quiz-opcion.incorrecta { border-color: var(--danger); background: #450a0a; } /* rojo */
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // --- Referencias ---
  const preguntaTextoEl = document.getElementById('quizPreguntaTexto');
  const opcionesEl = document.getElementById('quizOpciones');
  const btnSiguiente = document.getElementById('quizBtnSiguiente');
  const numActualEl = document.getElementById('quizNumActual');
  const numTotalEl = document.getElementById('quizNumTotal');
  const progressBar = document.getElementById('quizProgressBar');

  // --- Datos (vienen desde PHP) ---
  const preguntas = <?= $preguntasJson ?>;  // cada p tiene: id_pregunta, pregunta_texto, opciones{id=>def}, id_correcto
  let indicePreguntaActual = 0;
  let respuestasUsuario = []; // { id_pregunta, id_respuesta }
  let respondida = false;     // ¿ya fue respondida la pregunta actual?

  numTotalEl.textContent = preguntas.length;

  // --- Utilidades ---
  function setTextoBtn() {
    btnSiguiente.textContent = (indicePreguntaActual === preguntas.length - 1)
      ? 'Terminar Quiz y Ver Resultados'
      : 'Siguiente Pregunta';
  }

  function cargarPregunta(idx) {
    const p = preguntas[idx];
    preguntaTextoEl.textContent = p.pregunta_texto;
    opcionesEl.innerHTML = '';
    respondida = false;
    btnSiguiente.disabled = true;
    setTextoBtn();

    // Progreso
    numActualEl.textContent = idx + 1;
    progressBar.style.width = `${((idx) / preguntas.length) * 100}%`;

    // Crear opciones
    for (const idOpcion in p.opciones) {
      const boton = document.createElement('button');
      boton.className = 'quiz-opcion';
      boton.dataset.id = idOpcion;

      // texto + contenedor de icono
      const spanTxt = document.createElement('span');
      spanTxt.textContent = p.opciones[idOpcion];
      const spanIco = document.createElement('span');
      spanIco.className = 'quiz-ico';
      boton.appendChild(spanTxt);
      boton.appendChild(spanIco);

      opcionesEl.appendChild(boton);
    }
  }

  function bloquearOpciones() {
    opcionesEl.querySelectorAll('.quiz-opcion').forEach(b => b.disabled = true);
  }

  function habilitarOpciones() {
    opcionesEl.querySelectorAll('.quiz-opcion').forEach(b => b.disabled = false);
  }

  // --- Click en una opción (retroalimentación inmediata) ---
  opcionesEl.addEventListener('click', function(e) {
    const btn = e.target.closest('.quiz-opcion');
    if (!btn || respondida) return;

    const p = preguntas[indicePreguntaActual];
    const idSeleccionada = btn.dataset.id;
    const idCorrecta = String(p.id_correcto);

    // Guardar respuesta del usuario
    respuestasUsuario.push({
      id_pregunta: p.id_pregunta,
      id_respuesta: idSeleccionada
    });

    // Marcar visualmente
    const botones = [...opcionesEl.querySelectorAll('.quiz-opcion')];
    botones.forEach(b => {
      const ico = b.querySelector('.quiz-ico');
      if (b.dataset.id === idCorrecta) {
        b.classList.add('correcta');
        if (ico) ico.textContent = '✓';
      }
    });

    if (idSeleccionada !== idCorrecta) {
      // marcar la seleccionada como incorrecta
      btn.classList.add('incorrecta');
      const icoSel = btn.querySelector('.quiz-ico');
      if (icoSel) icoSel.textContent = '✗';
    }

    // Bloquea y habilita "Siguiente"
    bloquearOpciones();
    btnSiguiente.disabled = false;
    respondida = true;

    // Completa la barra al responder
    progressBar.style.width = `${((indicePreguntaActual + 1) / preguntas.length) * 100}%`;
  });

  // --- Click en Siguiente / Terminar ---
  btnSiguiente.addEventListener('click', async function() {
    if (!respondida) return; // seguridad

    // ¿Quedan preguntas?
    if (indicePreguntaActual < preguntas.length - 1) {
      indicePreguntaActual++;
      cargarPregunta(indicePreguntaActual);
      habilitarOpciones();
    } else {
      // Terminar y enviar
      btnSiguiente.disabled = true;
      btnSiguiente.textContent = 'Calificando...';
      try {
        const resp = await fetch('<?= url('/glosario/quiz/submit') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(respuestasUsuario)
        });
        const data = await resp.json();

        if (data.success) {
          Swal.fire({
            title: `¡Quiz Terminado! (${data.score})`,
            text: data.message,
            icon: 'success',
            background: 'var(--panel)',
            color: 'var(--text)'
          }).then(() => {
            window.location.href = '<?= url('/glosario') ?>';
          });
        } else {
          throw new Error(data.message || 'Fallo al calificar');
        }
      } catch (err) {
        Swal.fire('Error', 'No se pudo enviar tu quiz: ' + err.message, 'error');
        btnSiguiente.disabled = false;
        setTextoBtn();
      }
    }
  });

  // --- Inicial ---
  cargarPregunta(0);
  habilitarOpciones();
});
</script>

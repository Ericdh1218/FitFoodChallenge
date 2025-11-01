<?php
/** @var array $ejercicios */
/** @var array $actividadesNeat */
/** @var array $actividadDestacada */
/** @var array $conteos */
/** @var int $refrescosRestantes */
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<h1 style="margin-bottom: 8px;">Actividades</h1>
<p class="muted">Encuentra formas rápidas y fáciles de activar tu cuerpo, con o sin equipo.</p>

<!-- Reto NEAT del Día -->
<section class="section" style="margin-top: 24px; background: linear-gradient(135deg, var(--card), var(--panel)); border-color: var(--brand-2);">
    <h2 style="color: var(--brand-2);">✨ Tu Reto NEAT de Hoy ✨</h2>
    <div id="neatDestacadoContainer"></div>
    <div style="display: flex; gap: 10px; margin-top: 16px;">
  <button id="btnCompletarNeat" class="btn primary">¡Lo hice! (+5 XP)</button>
  <button id="btnRefrescarNeat" class="btn btn-switch">
    Cambiar <span id="refrescosCount" class="pill"><?= max(0, (int)$refrescosRestantes) ?></span>
  </button>
</div>

</section>

<!-- Ejercicios Rápidos -->
<section class="section" style="margin-top: 24px;">
    <h2>⏱️ Ideas de 5 Minutos (Sin Equipo)</h2>
    <div class="guia-grid" style="margin-top: 16px; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
        <?php partial('home/_partial_ejercicio_cards', ['ejercicios' => $ejercicios]); ?>
    </div>
</section>

<!-- Lista de Actividades NEAT -->
<section class="section" style="margin-top: 24px;">
    <h2>👟 Actividades Diarias (NEAT)</h2>
    <p class="muted">¡Todo suma! Incorpora estos pequeños movimientos en tu día a día.</p>
    <div id="neat-grid-container" class="grid cards neat-grid" style="margin-top: 16px;">
        <?php foreach ($actividadesNeat as $act): ?>
            <?php $conteo = $conteos[$act['id']] ?? 0; ?>
            <div class="card neat-card" id="neat-card-<?= $act['id'] ?>">
                <div class="neat-texto">
                    <h3><?= htmlspecialchars($act['titulo']) ?></h3>
                    <p class="muted"><?= htmlspecialchars($act['descripcion']) ?></p>
                </div>
                <div class="neat-accion">
                    <button class="btn primary btn-sm btn-completar-neat" data-actividad-id="<?= $act['id'] ?>">
                        ¡Hecho! (+5 XP)
                    </button>
                    <span class="tag tag-verde-claro neat-conteo <?= $conteo > 0 ? '' : 'hidden' ?>" style="margin-top: 8px;">
                        Completado <?= $conteo ?> <?= $conteo == 1 ? 'vez' : 'veces' ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>


<style>
.neat-grid {
    grid-template-columns: 1fr;
    gap: 12px;
}
@media (min-width: 600px) {
    .neat-grid { grid-template-columns: repeat(2, 1fr); }
}
.neat-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 16px;
}
.neat-texto h3 { margin: 0 0 4px 0; font-size: 1.1rem; }
.neat-texto p { margin: 0; font-size: 0.9rem; }
.neat-accion {
    margin-top: 16px;
    text-align: center;
}
.neat-accion .btn { width: 100%; margin-top: 0; }
.tag-verde-claro {
    background: rgba(34, 197, 94, 0.1);
    color: var(--brand);
    border-color: rgba(34, 197, 94, 0.2);
}
.hidden { display: none; }
#neatDestacadoContainer {
    padding: 16px;
    background: var(--card);
    border-radius: var(--radius-sm);
    margin-top: 16px;
    min-height: 100px;
    display: flex;
    align-items: center;
    gap: 16px;
}
#neatIcono { font-size: 3rem; line-height: 1; }
#neatTexto h3 { margin: 0 0 4px 0; font-size: 1.3rem; color: var(--text); }
#neatTexto p { margin: 0; color: var(--muted); }
/* Botón Cambiar: visible y con badge */
.btn-switch{
  background: rgba(45,212,191,.12);
  border: 1px solid #2dd4bf;
  color: #5eead4;
  font-weight: 800;
  padding: 12px 16px;
  border-radius: 14px;
  transition: all .15s ease;
}
.btn-switch:hover{
  background: rgba(45,212,191,.2);
  box-shadow: 0 8px 24px rgba(45,212,191,.2);
}
.btn-switch:disabled{
  opacity: .5;
  cursor: not-allowed;
  background: rgba(148,163,184,.12);
  border-color: #475569;
  color: #94a3b8;
}

/* Badge con número de cambios restantes */
.pill{
  display:inline-block;
  margin-left:8px;
  padding: 2px 10px;
  border-radius: 999px;
  font-weight: 900;
  background: rgba(45,212,191,.15);
  border: 1px solid #2dd4bf66;
  color: #99f6e4;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // --- Referencias UI ---
  const btnCompletar    = document.getElementById('btnCompletarNeat');
  const btnRefrescar    = document.getElementById('btnRefrescarNeat');
  const container       = document.getElementById('neatDestacadoContainer');

  // --- Estado inicial desde PHP ---
  let refrescosRestantes = <?= max(0, (int)$refrescosRestantes) ?>;
  let actividadActual    = <?= json_encode($actividadDestacada, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE); ?>;
  let completadoHoy      = false;

  // ---------- Helpers ----------
  function renderActividad(actividad) {
    actividadActual = actividad;
    container.innerHTML = `
      <div id="neatIcono">💡</div>
      <div id="neatTexto">
        <h3>${actividad.titulo}</h3>
        <p>${actividad.descripcion}</p>
      </div>
    `;
  }

  function setSwitchLabel(n) {
    // Rellena el botón con el badge y lo habilita/inhabilita
    btnRefrescar.innerHTML = `Cambiar <span id="refrescosCount" class="pill">${n}</span>`;
    btnRefrescar.disabled = n <= 0;
  }

  function setSwitchBusy(isBusy) {
    if (isBusy) {
      btnRefrescar.disabled = true;
      btnRefrescar.textContent = 'Buscando...';
    } else {
      setSwitchLabel(refrescosRestantes);
    }
  }

  // ---------- Render inicial ----------
  if (actividadActual && actividadActual.id) {
    renderActividad(actividadActual);
    setSwitchLabel(refrescosRestantes);
  } else {
    container.innerHTML = '<p class="muted">No hay actividades destacadas hoy.</p>';
    btnCompletar.disabled = true;
    btnRefrescar.disabled = true;
  }

  // ---------- Handlers ----------
  // Cambiar (refrescar) actividad destacada
  btnRefrescar?.addEventListener('click', async () => {
    if (refrescosRestantes <= 0) {
      Swal.fire({
        title: '¡Límite alcanzado!',
        text: 'Ya usaste tus 3 cambios de hoy. ¡Intenta esta actividad!',
        icon: 'warning',
        background: 'var(--panel)',
        color: 'var(--text)'
      });
      return;
    }

    setSwitchBusy(true);

    try {
      const response = await fetch('<?= url('/actividades/refrescar') ?>', {
        method: 'POST'
      });
      const data = await response.json();

      if (data && data.success) {
        if (data.actividad) renderActividad(data.actividad);

        // servidor devuelve 'refrescosRestantes'
        refrescosRestantes = Number.isFinite(data.refrescosRestantes)
          ? Math.max(0, data.refrescosRestantes)
          : Math.max(0, refrescosRestantes - 1);

        setSwitchBusy(false);
      } else {
        Swal.fire('Error', data?.message || 'No se pudo refrescar la actividad.', 'error');
        setSwitchBusy(false);
      }
    } catch (e) {
      console.error(e);
      Swal.fire('Error', 'No se pudo refrescar la actividad.', 'error');
      setSwitchBusy(false);
    }
  });

  // Completar actividad destacada (suma XP)
  btnCompletar?.addEventListener('click', async () => {
    if (completadoHoy || !actividadActual?.id) return;

    btnCompletar.disabled = true;
    completadoHoy = true;

    try {
      const body = new URLSearchParams();
      body.append('actividad_id', actividadActual.id);

      const response = await fetch('<?= url('/actividades/completar') ?>', {
        method: 'POST',
        body
      });
      const data = await response.json();

      if (data && data.success) {
        Swal.fire({
          title: '¡Genial!',
          text: data.message || `¡Actividad registrada! Ganaste ${data.xp ?? 5} XP.`,
          icon: 'success',
          background: 'var(--panel)',
          color: 'var(--text)'
        });
        btnCompletar.textContent = '¡Completado! ✓';
        // Opcional: ocultar el switch tras completar
        btnRefrescar.style.display = 'none';
      } else {
        Swal.fire('Error', data?.message || 'No se pudo registrar la actividad.', 'error');
        completadoHoy = false;
        btnCompletar.disabled = false;
      }
    } catch (e) {
      console.error(e);
      Swal.fire('Error', 'Error de conexión.', 'error');
      completadoHoy = false;
      btnCompletar.disabled = false;
    }
  });

  // Delegación para “¡Hecho! (+5 XP)” en la grilla de actividades
  const gridContainer = document.getElementById('neat-grid-container');
  if (gridContainer) {
    gridContainer.addEventListener('click', async function (event) {
      const boton = event.target.closest('.btn-completar-neat');
      if (!boton) return;
      event.preventDefault();

      const actividadId = boton.dataset.actividadId;
      const tarjeta = boton.closest('.neat-card');
      const conteoSpan = tarjeta?.querySelector('.neat-conteo');

      boton.disabled = true;
      const originalText = boton.textContent;
      boton.textContent = 'Guardando...';

      try {
        const body = new URLSearchParams();
        body.append('actividad_id', actividadId);

        const response = await fetch('<?= url('/actividades/completar') ?>', {
          method: 'POST',
          body
        });
        const data = await response.json();

        if (data && data.success) {
          Swal.fire({
            title: '¡Genial!',
            text: data.message || 'Actividad registrada',
            icon: 'success',
            toast: true,
            position: 'top-end',
            timer: 3000,
            background: 'var(--panel)',
            color: 'var(--text)',
            showConfirmButton: false
          });

          const nuevoTotal = Number.isFinite(data.nuevoTotal) ? data.nuevoTotal : 1;
          if (conteoSpan) {
            conteoSpan.textContent = `Completado ${nuevoTotal} ${nuevoTotal == 1 ? 'vez' : 'veces'}`;
            conteoSpan.classList.remove('hidden');
          }
        } else {
          Swal.fire('Error', data?.message || 'No se pudo registrar la actividad.', 'error');
        }
      } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Error de conexión.', 'error');
      } finally {
        boton.disabled = false;
        boton.textContent = originalText;
      }
    });
  }
});
</script>

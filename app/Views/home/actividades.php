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
        <button id="btnRefrescarNeat" class="btn ghost">
            Cambiar (quedan <span id="refrescosCount"><?= $refrescosRestantes ?></span>)
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnCompletar = document.getElementById('btnCompletarNeat');
    const btnRefrescar = document.getElementById('btnRefrescarNeat');
    const refrescosCountSpan = document.getElementById('refrescosCount');
    const container = document.getElementById('neatDestacadoContainer');
    let refrescosRestantes = <?= $refrescosRestantes ?>;
    let actividadActual = <?= json_encode($actividadDestacada, JSON_HEX_TAG); ?>;
    let completadoHoy = false;

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



    if (actividadActual) {
        renderActividad(actividadActual);
    } else {
        container.innerHTML = '<p class="muted">No hay actividades destacadas hoy.</p>';
        btnCompletar.disabled = true;
        btnRefrescar.disabled = true;
    }

    btnRefrescar?.addEventListener('click', async () => {
        if (refrescosRestantes <= 0) {
            Swal.fire({ title: '¡Límite alcanzado!', text: 'Ya usaste tus 3 cambios de hoy. ¡Intenta esta actividad!', icon: 'warning', background: 'var(--panel)', color: 'var(--text)' });
            return;
        }

        btnRefrescar.disabled = true;
        btnRefrescar.textContent = 'Buscando...';

        try {
            const response = await fetch('<?= url('/actividades/refrescar') ?>');
            const data = await response.json();

            if (data.success) {
                renderActividad(data.actividad);
                refrescosRestantes = data.refrescosRestantes;
                refrescosCountSpan.textContent = refrescosRestantes;
                btnRefrescar.textContent = `Cambiar (quedan \${refrescosRestantes})`;
                if (refrescosRestantes <= 0) btnRefrescar.disabled = true;
                else btnRefrescar.disabled = false;
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'No se pudo refrescar la actividad.', 'error');
            btnRefrescar.disabled = false;
        }
    });

    btnCompletar?.addEventListener('click', async () => {
        if (completadoHoy) return;
        btnCompletar.disabled = true;
        completadoHoy = true;

        try {
            const formData = new URLSearchParams();
            formData.append('actividad_id', actividadActual.id);

            const response = await fetch('<?= url('/actividades/completar') ?>', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                Swal.fire({ title: '¡Genial!', text: data.message, icon: 'success', background: 'var(--panel)', color: 'var(--text)' });
                btnCompletar.textContent = '¡Completado! ✓';
                btnRefrescar.style.display = 'none';
            } else {
                Swal.fire('Error', data.message, 'error');
                completadoHoy = false;
                btnCompletar.disabled = false;
            }
        } catch (e) {
            Swal.fire('Error', 'Error de conexión.', 'error');
            completadoHoy = false;
            btnCompletar.disabled = false;
        }
    });

    const gridContainer = document.getElementById('neat-grid-container');
    if (gridContainer) {
        gridContainer.addEventListener('click', async function(event) {
            if (!event.target.classList.contains('btn-completar-neat')) return;
            event.preventDefault();

            const boton = event.target;
            const actividadId = boton.dataset.actividadId;
            const tarjeta = boton.closest('.neat-card');
            const conteoSpan = tarjeta.querySelector('.neat-conteo');

            boton.disabled = true;
            boton.textContent = 'Guardando...';

            try {
                const formData = new URLSearchParams();
                formData.append('actividad_id', actividadId);

                const response = await fetch('<?= url('/actividades/completar') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire({ title: '¡Genial!', text: data.message, icon: 'success', toast: true, position: 'top-end', timer: 3000, background: 'var(--panel)', color: 'var(--text)', showConfirmButton: false });
                    const nuevoTotal = data.nuevoTotal;
                    if (conteoSpan) {
                        conteoSpan.textContent = `Completado ${nuevoTotal} ${nuevoTotal == 1 ? 'vez' : 'veces'}`;
                        conteoSpan.classList.remove('hidden');
                    }
                    setTimeout(() => {
                        boton.disabled = false;
                        boton.textContent = '¡Hecho! (+5 XP)';
                    }, 1000);
                } else {
                    Swal.fire('Error', data.message, 'error');
                    boton.disabled = false;
                    boton.textContent = '¡Hecho! (+5 XP)';
                }
            } catch (e) {
                Swal.fire('Error', 'Error de conexión.', 'error');
                boton.disabled = false;
                boton.textContent = '¡Hecho! (+5 XP)';
            }
        });
    }
});
</script>
<?php 
/** @var array $rutina */
/** @var array $ejerciciosEnRutina */ 
/** @var array $ejerciciosDisponibles */
/** @var bool $editMode */ 

// --- AÑADIDO: Lógica para Mensaje Flash ---
$mensajeFlash = null;
if (isset($_SESSION['flash_message'])) {
    $mensajeFlash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Limpia el mensaje
}
// --- FIN LÓGICA FLASH ---
?>

<a class="link" href="<?= url('/rutinas') ?>">← Volver a Rutinas</a>

<?php if ($mensajeFlash): ?>
    <div class="flash-info card" style="background: var(--brand); color: var(--bg); margin: 20px 0 0 0; text-align: center; padding: 16px;">
        <?= htmlspecialchars($mensajeFlash) ?>
    </div>
<?php endif; ?>
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1 style="margin-top: 8px;">
        <?= $editMode ? 'Editando:' : 'Viendo:' ?> <?= htmlspecialchars($rutina['nombre_rutina']) ?>
    </h1>
    <?php if (!$editMode): ?>
        <a href="<?= url('/rutina/editar?id=' . $rutina['id'] . '&modo=editar') ?>" class="btn primary">
            ✏️ Editar Rutina
        </a>
    <?php endif; ?>
</div>

<div class="layout-sidebar-wrapper" style="margin-top: 24px;">

    <aside class="layout-sidebar">
        <h2>Ejercicios en esta Rutina</h2>
        
        <div id="listaEjerciciosEnRutina">
            <?php if (empty($ejerciciosEnRutina)): ?>
                <p class="muted">Aún no has añadido ejercicios. <?php if ($editMode) echo 'Usa la sección de la derecha para añadir.'; ?></p>
            <?php else: ?>
                <table class="table" style="margin-top: 16px;">
                    
                    <thead>
                        <tr>
                            <th>Ejercicio</th>
                            <th>Series</th>
                            <th>Reps</th>
                            <th></th> </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ejerciciosEnRutina as $ej): ?>
                            <tr data-rutina-ejercicio-id="<?= $ej['rutina_ejercicio_id'] ?>">
                                <td>
                                    <?php
                                        $returnUrl = urlencode(url('/rutina/editar?id=' . $rutina['id'])); 
                                        $detalleEjercicioUrl = url('/deportes?id=' . $ej['id'] . '&return_url=' . $returnUrl);
                                        $imgSrc = null;
                                        if (!empty($ej['media_url'])) {
                                            $img = $ej['media_url'];
                                            if (!preg_match('~^https?://~', $img)) {
                                                $imgSrc = url('assets/img/' . $img); 
                                            } else {
                                                $imgSrc = $img; 
                                            }
                                        }
                                    ?>
                                    <a href="<?= htmlspecialchars($detalleEjercicioUrl) ?>" class="link-ejercicio-tabla"> 
                                        <?php if($imgSrc): ?>
                                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 40px; height: 40px; background: var(--card); border-radius: 4px; display: inline-block;"></div>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($ej['nombre']) ?></span>
                                    </a>
                                </td>
                                
                                <td><?= htmlspecialchars($ej['series'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($ej['repeticiones'] ?? '-') ?></td>
                                <td>
                                    <?php if ($editMode): ?>
                                    <button class="btn ghost btn-sm btn-quitar" style="color: var(--danger); border: none; padding: 4px;">
                                        Quitar
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
         <a href="<?= url('/rutinas') ?>" class="btn primary" style="width: 100%; margin-top: 24px;">
            Listo, volver a Rutinas
        </a>
    </aside>

    <?php if ($editMode): ?>
        <main class="layout-content">
            <h2>Añadir Ejercicios</h2>
            <p class="muted">Busca en la biblioteca y haz clic para añadir.</p>
            
            <input type="text" id="searchInput" class="input" placeholder="Buscar ejercicio..." style="margin-bottom: 16px;">

            <div id="listaEjerciciosDisponibles">
                <?php foreach ($ejerciciosDisponibles as $ej): ?>
                    <div class="ejercicio-disponible card" data-ejercicio-id="<?= $ej['id'] ?>">
                        <div>
                             <h4><?= htmlspecialchars($ej['nombre']) ?></h4>
                             <p class="muted" style="font-size: 0.9em;">
                                 <?= htmlspecialchars($ej['grupo_muscular'] ?? '') ?> • 
                                 <?= htmlspecialchars($ej['equipamiento'] ?? '') ?>
                             </p>
                        </div>
                        <button class="btn primary btn-sm btn-anadir" 
                                data-rutina-id="<?= $rutina['id'] ?>" 
                                data-ejercicio-id="<?= $ej['id'] ?>">
                           Añadir +
                       </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    <?php endif; // Fin del if ($editMode) ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ... (Tu script existente para buscar, añadir y quitar ejercicios) ...
    
    const searchInput = document.getElementById('searchInput');
    const listaDisponibles = document.getElementById('listaEjerciciosDisponibles');
    const todosLosDisponibles = listaDisponibles ? listaDisponibles.querySelectorAll('.ejercicio-disponible') : [];
    const tablaEnRutinaBody = document.querySelector('#listaEjerciciosEnRutina tbody');

    // --- 1. Filtro de Búsqueda ---
    if(searchInput && todosLosDisponibles.length > 0) {
        searchInput.addEventListener('input', function() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            todosLosDisponibles.forEach(item => {
                const nombreEjercicio = item.querySelector('h4').textContent.toLowerCase();
                item.style.display = nombreEjercicio.includes(searchTerm) ? 'flex' : 'none';
            });
        });
    }

    // --- 2. Lógica para AÑADIR ejercicio ---
    if(listaDisponibles) {
        listaDisponibles.addEventListener('click', async function(event) {
            if (!event.target.classList.contains('btn-anadir')) { return; }
            
            const boton = event.target;
            const rutinaId = boton.dataset.rutinaId;
            const ejercicioId = boton.dataset.ejercicioId;
            const nombreEjercicio = boton.closest('.ejercicio-disponible').querySelector('h4').textContent;

            // Preguntar por series y repeticiones
            const { value: formValues } = await Swal.fire({
                title: `Añadir "${nombreEjercicio}"`,
                html: `
                    <input id="swal-input-series" class="swal2-input" placeholder="Series (ej: 3)">
                    <input id="swal-input-reps" class="swal2-input" placeholder="Repeticiones (ej: 8-12)">`,
                focusConfirm: false,
                confirmButtonText: 'Añadir a la Rutina',
                cancelButtonText: 'Cancelar',
                showCancelButton: true,
                background: 'var(--panel)', 
                color: 'var(--text)',
                preConfirm: () => {
                    const series = document.getElementById('swal-input-series').value;
                    const reps = document.getElementById('swal-input-reps').value;
                    if (!series || !reps) {
                        Swal.showValidationMessage(`Por favor, ingresa series y repeticiones`);
                        return false;
                    }
                    return { series: series, reps: reps };
                }
            });

            // Si el usuario confirmó
            if (formValues) {
                const series = formValues.series;
                const reps = formValues.reps;

                try {
                    const response = await fetch('<?= url('/rutina/agregar-ejercicio') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams({ 
                            rutina_id: rutinaId, 
                            ejercicio_id: ejercicioId, 
                            series: series, 
                            repeticiones: reps 
                        })
                    });

                    if (!response.ok) { throw new Error(`Error del servidor: ${response.statusText}`); }
                    const result = await response.json(); 

                    if (result.success) {
                        location.reload(); // Recarga la página para ver el ejercicio añadido
                    } else {
                        Swal.fire('Error', result.message || 'No se pudo añadir el ejercicio.', 'error');
                    }
                } catch (error) {
                    console.error('Error al añadir ejercicio:', error);
                    Swal.fire('Error', 'Ocurrió un problema de conexión.', 'error');
                }
            }
        });
    } // Fin if(listaDisponibles)
    
    // --- 3. Lógica para QUITAR ejercicio ---
    if (tablaEnRutinaBody) {
        tablaEnRutinaBody.addEventListener('click', async function(event){
            if (!event.target.classList.contains('btn-quitar')) { return; }

            event.preventDefault(); 
            const botonQuitar = event.target;
            const fila = botonQuitar.closest('tr'); 
            const rutinaEjercicioId = fila.dataset.rutinaEjercicioId; 
            const nombreEjercicio = fila.querySelector('td:first-child').textContent.trim(); 

            // Confirmación
            const result = await Swal.fire({
                title: '¿Quitar Ejercicio?',
                text: `¿Seguro que quieres quitar "${nombreEjercicio}" de esta rutina?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar',
                background: 'var(--panel)', 
                color: 'var(--text)',
            });

            if (result.isConfirmed) {
                // Enviar AJAX para eliminar
                try {
                    const response = await fetch('<?= url('/rutina/quitar-ejercicio') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams({ 
                            rutina_ejercicio_id: rutinaEjercicioId 
                        })
                    });

                    if (!response.ok) throw new Error(`Error del servidor: ${response.statusText}`);
                    
                    const resJson = await response.json();

                    if (resJson.success) {
                        fila.remove(); // Elimina la fila visualmente
                    } else {
                        Swal.fire('Error', resJson.message || 'No se pudo quitar el ejercicio.', 'error');
                    }
                } catch (error) {
                    console.error('Error al quitar ejercicio:', error);
                    Swal.fire('Error', 'Ocurrió un problema de conexión.', 'error');
                }
            }
        });
    } // Fin if(tablaEnRutinaBody)
});
</script>
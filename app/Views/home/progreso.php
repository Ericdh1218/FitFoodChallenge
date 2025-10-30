<?php 
/** @var array $usuario */ 
/** @var array|null $registroHoy */ 
/** @var string $pesoDataJs */
/** @var string $habitosDataJs */
/** @var array $fotos */
/** @var string $nombreNivel */
/** @var int $xpActual */
/** @var int $xpSiguienteNivel */
/** @var int $xpInicioNivel */
/** @var float $porcentajeProgreso */
/** @var array $insignias */ // <-- AÑADE ESTA LÍNEA

// Pre-procesar historial para combinarlo y ordenarlo por fecha
$historial = [];
foreach ($historialPeso as $r) {
    $historial[strtotime($r['fecha'])] = ['tipo' => 'peso', 'data' => $r];
}
foreach ($historialHabitos as $r) {
    // Evitar sobreescribir si hay registro de peso el mismo día
    $key = strtotime($r['fecha']);
    while(isset($historial[$key])) { $key++; } 
    $historial[$key] = ['tipo' => 'habito', 'data' => $r];
}
krsort($historial); // Ordenar por fecha (más reciente primero)
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<h1 style="margin-bottom: 8px;">Mi Progreso</h1>
<p class="muted">Registra tus hábitos diarios y observa tu evolución.</p>

<?php 
$mensajeFlash = null;
if (isset($_SESSION['flash_message'])) {
    $mensajeFlash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); 
}
?>
<?php if ($mensajeFlash): ?>
    <div class="flash-info card" style="background: var(--brand); color: var(--bg); margin-bottom: 16px;">
        <?= htmlspecialchars($mensajeFlash) ?>
    </div>
<?php endif; ?>
<div class="layout-sidebar-wrapper" style="align-items: flex-start;">

    <main class="layout-content">
        
        <div class="section"> 
            
            <h2 class="auth-title" style="color: var(--text); font-size: 24px;">Check-in de Hoy (<?= date('d/m/Y') ?>)</h2>
            
            <?php if ($registroHoy): // Si YA registró hoy ?>
                <p class="checkin-completo">
                    ¡Ya registraste tus hábitos de hoy! 👍
                </p>
                <div class="habitos-registrados">
                    <?php if(!empty($registroHoy['agua_cumplido'])) echo '<span class="tag tag-verde">💧 Agua</span>'; ?>
                    <?php if(!empty($registroHoy['sueno_cumplido'])) echo '<span class="tag tag-verde">😴 Sueño</span>'; ?>
                    <?php if(!empty($registroHoy['entrenamiento_cumplido'])) echo '<span class="tag tag-verde">🏋️ Entrenamiento</span>'; ?>
                </div>
                <p style="text-align:center; margin-top: 16px;">
                     <a href="<?= url('/progreso/checkin/editar') // Ruta futura para editar ?>" class="link muted">(Editar registro de hoy)</a> 
                </p>

            <?php else: // Si NO ha registrado hoy, muestra el formulario ?>
                <p class="auth-subtitle" style="color: var(--muted);">
                    ¿Cómo te fue hoy, <?= htmlspecialchars($usuario['nombre']) ?>?
                </p>
                <form action="<?= url('/progreso/checkin') ?>" method="POST" style="margin-top: 24px;">
                    <div class="form-check-group">
                        <input type="checkbox" id="agua" name="agua_cumplido" value="1">
                        <label for="agua">
                            <strong>Agua:</strong> 
                            <span>Completaste tu meta de <?= (int)($usuario['consumo_agua'] ?? 0) ?> vasos.</span>
                        </label>
                    </div>
                    <div class="form-check-group">
                        <input type="checkbox" id="sueno" name="sueno_cumplido" value="1">
                        <label for="sueno">
                            <strong>Sueño:</strong> 
                            <span>Dormiste tus <?= (int)($usuario['horas_sueno'] ?? 0) ?> horas.</span>
                        </label>
                    </div>
                    <div class="form-check-group">
                        <input type="checkbox" id="entrenamiento" name="entrenamiento_cumplido" value="1">
                        <label for="entrenamiento">
                            <strong>Entrenamiento:</strong> 
                            <span>Realizaste tu rutina de ejercicio de hoy.</span>
                        </label>
                    </div>
                    <button type="submit" class="btn primary" style="width:100%; margin-top: 24px;">
                        Guardar mi Progreso ✅
                    </button>
                </form>
            <?php endif; ?>
            
        </div>
        <div class="section" style="margin-top: 24px;">
    <h2>Evolución de Peso</h2>
    <div style="position: relative; height: 300px;">
        <canvas id="graficaPeso"></canvas>
    </div>
</div>


        <div class="section" style="margin-top: 24px;">
            <h2>Racha de Hábitos (Últimos 30 días)</h2>
            <div style="position: relative; height: 300px;">
                <canvas id="graficaHabitos"></canvas> 
            </div>
            </div>
            <div style="text-align: center; margin-top: 10px; font-size: 14px; color: #ffffff;">
    <strong>Nota:</strong> <span style="background-color: #000; padding: 2px 6px; border-radius: 4px;">1</span> = Cumplido, 
    <span style="background-color: #000; padding: 2px 6px; border-radius: 4px;">0</span> = No cumplido
</div>


    </main>
    <aside class="layout-sidebar">
        <div class="section player-summary" style="margin-bottom: 24px;">
            <h2 style="color: var(--brand-2);">Tu Progreso</h2>
            
            <div class="player-level" style="margin-top: 16px;">
                <div style="font-size: 0.9em; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px;">Nivel Actual</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--brand-2); margin-top: 4px;">
                    <?= (int)$usuario['level'] ?>: <?= htmlspecialchars($nombreNivel) ?>
                </div>
            </div>

            <div class="player-xp" style="margin-top: 16px;">
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: <?= round($porcentajeProgreso, 2) ?>%;">
                        <span><?= round($porcentajeProgreso) ?>%</span>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9em; margin-top: 8px;">
                    <span style="color: var(--muted); font-weight: 600;"><?= (int)$xpActual ?> XP</span>
                    <?php if ($xpSiguienteNivel > $xpActual): // Muestra 'siguiente' solo si no es nivel max ?>
                        <span style="color: var(--muted);">Siguiente: <?= (int)$xpSiguienteNivel ?> XP</span>
                    <?php else: ?>
                         <span style="color: var(--brand);">¡Nivel Máximo!</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="section player-summary" style="margin-bottom: 24px;">
            </div>

        <div class="section" style="margin-bottom: 24px;">
            <h2>🏆 Mis Insignias</h2>
            
            <?php if (empty($insignias)): ?>
                <p class="muted historial-vacio-mensaje" style="margin: 0; padding: 10px 0;">
                    Aún no has ganado ninguna insignia. ¡Sigue completando retos!
                </p>
            <?php else: ?>
                <div class="insignia-lista">
                    <?php foreach ($insignias as $insignia): ?>
                        <div class="insignia-item" title="<?= htmlspecialchars($insignia['descripcion']) ?> - Obtenida el <?= date('d/m/Y', strtotime($insignia['fecha_obtenida'])) ?>">
                            <img src="<?= url('assets/img/iconos_insignias/' . htmlspecialchars($insignia['icono_url'])) ?>" alt="<?= htmlspecialchars($insignia['nombre']) ?>">
                            <span><?= htmlspecialchars($insignia['nombre']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="section" style="margin-bottom: 24px;">
            <h2>Actualizar Peso</h2>
            <form action="<?= url('/progreso/guardar-peso') ?>" method="POST">
                <div class="form-group">
                    <label for="peso_nuevo">Mi nuevo peso (kg)</label>
                    <input type="number" id="peso_nuevo" name="peso" class="input" placeholder="Ej: 69.5" step="0.1" required>
                </div>
                <button type="submit" class="btn primary" style="width: 100%;">Guardar Peso</button>
            </form>
        </div>

        <div class="section" style="margin-bottom: 24px;">
            <h2>Galería de Progreso</h2>
            <form action="<?= url('/progreso/subir-foto') ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="foto_progreso">Subir nueva foto (Max 5MB)</label>
                    <input type="file" name="foto_progreso" id="foto_progreso" class="input" accept="image/jpeg, image/png" required>
                </div>
                <div class="form-group">
                    <label for="foto_nota">Nota (opcional)</label>
                    <input type="text" name="nota" id="foto_nota" class="input" placeholder="Ej: Semana 1">
                </div>
                <button type="submit" class="btn primary" style="width: 100%;">Subir Foto</button>
            </form>

            <div class="galeria-progreso" id="galeriaProgresoContainer" style="margin-top: 20px;"> <?php if (empty($fotos)): ?>
                    <p class="muted" style="text-align: center;">Aún no has subido fotos.</p>
                <?php else: ?>
                    <?php foreach ($fotos as $foto): ?>
                        <div class="foto-item" id="foto-item-<?= $foto['id'] ?>">
                            <img src="<?= url('assets/uploads/progreso/' . htmlspecialchars($foto['nombre_archivo'])) ?>" alt="<?= htmlspecialchars($foto['nota']) ?>">
                            
                            <button class="btn-delete-foto" data-foto-id="<?= $foto['id'] ?>" title="Eliminar foto">
                                &times;
                            </button>
                            <div class="foto-info">
                                <strong><?= date('d/m/Y', strtotime($foto['fecha_subida'])) ?></strong>
                                <small><?= htmlspecialchars($foto['nota']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="section">
            <h2 style="color: var(--brand-2);">Feed de Actividad</h2> 
            <?php if (empty($historial)): ?>
                <p class="muted historial-vacio-mensaje">Aún no hay registros en tu historial.</p>
            <?php else: ?>
                <ul class="feed-lista">
                    
                    <?php foreach ($historial as $item): ?>
                        <?php if ($item['tipo'] === 'peso'): ?>
                            <li class="feed-item">
                                <div class="feed-icono" style="background: #a855f7;">⚖️</div>
                                <div class="feed-texto">
                                    Registraste un nuevo peso: <strong><?= htmlspecialchars($item['data']['peso']) ?> kg</strong>
                                    <small><?= date('d/m/Y', strtotime($item['data']['fecha'])) ?></small>
                                </div>
                            </li>
                        <?php elseif ($item['tipo'] === 'habito'): 
                            $habitos = [];
                            // === CORRECCIÓN DEL ERROR AQUÍ ===
                            // Usamos las claves renombradas por el modelo: 'agua', 'sueno', 'entrenamiento'
                            if(!empty($item['data']['agua'])) $habitos[] = '💧 Agua';
                            if(!empty($item['data']['sueno'])) $habitos[] = '😴 Sueño';
                            if(!empty($item['data']['entrenamiento'])) $habitos[] = '🏋️ Entrenamiento';
                            // ==================================
                            if (empty($habitos)) continue; 
                        ?>
                            <li class="feed-item">
                                <div class="feed-icono" style="background: var(--brand);">✓</div>
                                <div class="feed-texto">
                                    Check-in: <strong><?= implode(', ', $habitos) ?></strong>
                                    <small><?= date('d/m/Y', strtotime($item['data']['fecha'])) ?></small>
                                </div>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        </aside>
    </div>

<style>
.galeria-progreso { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 16px; }
.foto-item { position: relative; border-radius: var(--radius-sm); overflow: hidden; aspect-ratio: 1 / 1; }
.foto-item img { width: 100%; height: 100%; object-fit: cover; }
.foto-item .foto-info { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 12px 8px 8px 8px; color: white; }
.foto-item .foto-info strong { display: block; font-size: 0.9em; }
.foto-item .foto-info small { font-size: 0.8em; opacity: 0.8; }
.flash-info { padding: 12px 16px; font-weight: 500; }
.feed-lista { list-style: none; padding: 0; margin-top: 16px; max-height: 400px; overflow-y: auto; }
.feed-item { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--line); }
.feed-item:last-child { border-bottom: none; margin-bottom: 0; }
.feed-icono { width: 36px; height: 36px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 1.1rem; color: white; }
.feed-texto { font-size: 0.9rem; color: var(--text); line-height: 1.4; }
.feed-texto strong { font-weight: 600; }
.feed-texto small { display: block; color: var(--muted); font-size: 0.8rem; margin-top: 2px; }
.historial-vacio-mensaje { text-align: center; font-style: italic; margin: 10px 0; padding: 10px 0; color: var(--muted); }
/* (Estilos para .checkin-completo, .habitos-registrados, .tag-verde, .form-check-group ya deberían estar en tu main.css) */
.progress-bar-container {
    width: 100%;
    height: 24px;
    background: var(--card); /* Fondo de la barra */
    border-radius: 99px;
    border: 1px solid var(--line);
    overflow: hidden;
    position: relative;
}
.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--brand-2) 0%, var(--brand) 100%); /* Gradiente cian a verde */
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--bg); /* Texto oscuro */
    transition: width 0.5s ease-in-out;
}
.progress-bar-fill span {
    /* Sombra para que el texto resalte */
    filter: drop-shadow(0 1px 1px rgba(0,0,0,0.4));
}
.insignia-lista {
    display: grid;
    /* 3 columnas de iconos */
    grid-template-columns: repeat(3, 1fr); 
    gap: 16px;
    margin-top: 16px;
}
.insignia-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    cursor: help; /* Muestra un '?' al pasar el mouse por el 'title' */
}
.insignia-item img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    margin-bottom: 8px;
    /* Efecto de "ganada" */
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    transition: transform 0.2s ease;
}
.insignia-item:hover img {
    transform: scale(1.1);
}
.insignia-item span {
    font-size: 0.85rem;
    color: var(--muted);
    font-weight: 500;
    line-height: 1.3;
}
</style>
<!-- Plugin opcional para mostrar valores encima de los puntos -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof ChartDataLabels !== 'undefined') {
        Chart.register(ChartDataLabels);
    }

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                ticks: { color: '#ffffff' },
                grid: { display: false }
            },
            y: {
                ticks: {
                    color: '#ffffff',
                    callback: (value) => value + ' kg'
                },
                grid: { color: '#1f2937' }
            }
        },
        plugins: {
            legend: {
                labels: { color: '#ffffff' }
            },
            tooltip: {
                backgroundColor: '#1f2937',
                titleColor: '#ffffff',
                bodyColor: '#9aa5b1',
                callbacks: {
                    label: function(context) {
                        return `Peso: ${context.parsed.y} kg`;
                    }
                }
            },
            datalabels: {
                color: '#ffffff',
                backgroundColor: 'rgba(0,0,0,0.7)',
                borderColor: '#ffffff',
                borderWidth: 1,
                borderRadius: 4,
                padding: 6,
                font: {
                    weight: 'bold',
                    size: 12
                },
                anchor: 'center',
                align: 'center',
                formatter: function(value) {
                    return value > 0 ? value : '';
                }
            }
        }
    };

    function showNoDataMessage(canvasId, message = 'Aún no hay datos suficientes para mostrar esta gráfica.') {
        const canvas = document.getElementById(canvasId);
        if (!canvas || !canvas.parentElement) return;

        const noDataEl = document.createElement('p');
        noDataEl.textContent = message;
        noDataEl.className = 'muted';
        noDataEl.style.textAlign = 'center';
        noDataEl.style.padding = '40px 20px';

        canvas.parentElement.appendChild(noDataEl);
        canvas.style.display = 'none';
    }

    // === GRÁFICA DE PESO ===
    const ctxPeso = document.getElementById('graficaPeso');
    const pesoData = <?= $pesoDataJs ?? '[]' ?>;

    if (ctxPeso && pesoData.length > 1) {
        new Chart(ctxPeso, {
            type: 'line',
            data: {
                labels: pesoData.map(row => row.fecha),
                datasets: [{
                    label: 'Peso (kg)',
                    data: pesoData.map(row => row.peso),
                    borderColor: '#ffffff',
                    backgroundColor: 'rgba(217, 226, 203, 0.25)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#ffffff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    borderWidth: 3
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: false,
                        suggestedMin: Math.min(...pesoData.map(r => r.peso)) - 2,
                        suggestedMax: Math.max(...pesoData.map(r => r.peso)) + 2,
                        ticks: {
                            color: '#ffffff',
                            callback: (value) => value + ' kg'
                        },
                        grid: { color: '#1f2937' }
                    },
                    x: chartOptions.scales.x
                },
                plugins: {
                    ...chartOptions.plugins,
                    title: {
                        display: true,
                        text: 'Peso Corporal (últimos días)',
                        color: '#ffffff',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: { top: 10, bottom: 20 }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    } else if (ctxPeso) {
        const msg = pesoData.length === 1
            ? 'Necesitas al menos 2 registros de peso para ver la evolución.'
            : 'Aún no hay datos suficientes para mostrar esta gráfica.';
        showNoDataMessage('graficaPeso', msg);
    }

    // === GRÁFICA DE HÁBITOS ===
    const ctxHabitos = document.getElementById('graficaHabitos');
    const habitosData = <?= $habitosDataJs ?? '[]' ?>;

    if (ctxHabitos && habitosData.length > 0) {
        let habitosOptions = JSON.parse(JSON.stringify(chartOptions));
        habitosOptions.scales.x.stacked = true;
        habitosOptions.scales.y.stacked = true;
        habitosOptions.scales.y.ticks.stepSize = 1;

        new Chart(ctxHabitos, {
            type: 'bar',
            data: {
                labels: habitosData.map(row => row.fecha),
                datasets: [
                    {
                        label: 'Agua 💧',
                        data: habitosData.map(row => row.agua),
                        backgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Sueño 🛌',
                        data: habitosData.map(row => row.sueno),
                        backgroundColor: '#8b5cf6'
                    },
                    {
                        label: 'Entrenamiento 💪',
                        data: habitosData.map(row => row.entrenamiento),
                        backgroundColor: '#f97316'
                    }
                ]
            },
            options: {
                ...habitosOptions,
                plugins: {
                    ...habitosOptions.plugins,
                    title: {
                        display: true,
                        text: 'Racha de Hábitos (últimos 30 días)',
                        color: '#ffffff',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: { top: 10, bottom: 20 }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    } else if (ctxHabitos) {
        showNoDataMessage('graficaHabitos');
    }

    const galeriaContainer = document.getElementById('galeriaProgresoContainer');
    
    if (galeriaContainer) {
        galeriaContainer.addEventListener('click', function(event) {
            // Solo reacciona si se hizo clic en el botón de borrar
            if (!event.target.classList.contains('btn-delete-foto')) {
                return;
            }
            
            event.preventDefault(); // Previene cualquier acción por defecto
            const boton = event.target;
            const fotoId = boton.dataset.fotoId;
            const fotoItem = boton.closest('.foto-item'); // El div que contiene la foto

            // Confirmación con SweetAlert
            Swal.fire({
                title: '¿Eliminar esta foto?',
                text: "Esta acción no se puede revertir.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: 'var(--panel)', // Estilo oscuro
                color: 'var(--text)',
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Si confirma, enviar petición AJAX
                    try {
                        const formData = new URLSearchParams();
                        formData.append('foto_id', fotoId);

                        const response = await fetch('<?= url('/progreso/eliminar-foto') ?>', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) throw new Error('Error de red');
                        
                        const data = await response.json();

                        if (data.success) {
                            // Éxito: Elimina la foto de la vista
                            fotoItem.remove(); 
                            // (Opcional) Mostrar un toast de éxito
                            // Swal.fire('¡Eliminada!', 'Tu foto ha sido eliminada.', 'success');
                        } else {
                            Swal.fire('Error', data.message || 'No se pudo eliminar la foto.', 'error');
                        }
                    } catch (error) {
                        console.error('Error al eliminar foto:', error);
                        Swal.fire('Error', 'Error de conexión.', 'error');
                    }
                }
            });
        });
    }
});
</script>


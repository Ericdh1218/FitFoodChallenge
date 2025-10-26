<?php
/** @var array $area */
/** @var array $recetas */
?>

<a class="link" href="<?= url('/habitos') ?>">← Volver a Hábitos</a>
<div class="plan-header" style="text-align: center; margin: 16px 0 32px 0;">
    <span class="area-icono" style="font-size: 2.5rem;"><?= htmlspecialchars($area['icono'] ?? '🎯') ?></span>
    <h1 style="margin: 8px 0;"><?= htmlspecialchars($area['titulo']) ?></h1>
    <p class="muted"><?= htmlspecialchars($area['descripcion_corta']) ?></p>
</div>

<!-- --- Módulo 1: Video --- -->
<section class="section">
    <h2>🎥 ¿Qué pasa si no desayunas?</h2>
    <p class="muted">Saltarse el desayuno puede afectar tu concentración y hacer que tengas más antojos durante el día. ¡Mira este video corto!</p>
    <!-- Video de YouTube de ejemplo (reemplaza con el que quieras) -->
    <div class="video-container" style="aspect-ratio: 16/9; margin-top: 16px;">
        <iframe src="https://www.youtube.com/embed/wtth8oEE5z0" frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
        </iframe>
    </div>
</section>

<!-- --- Módulo 2: Simulador de Desayuno --- -->
<section class="section" style="margin-top: 24px;">
    <h2>🍳 Simulador de Desayuno</h2>
    <p class="muted">Haz clic en una opción para ver su impacto en tu energía.</p>
    <div class="simulador-opciones">
        <button class="simulador-btn" data-impacto="energia-baja">🥤 Refresco + Pan Dulce</button>
        <button class="simulador-btn" data-impacto="energia-media">🥣 Cereal de Caja</button>
        <button class="simulador-btn" data-impacto="energia-alta">🥚 Huevo + Fruta</button>
    </div>
    <div id="simuladorResultado" class="simulador-resultado card" style="display:none; margin-top: 16px;">
        <h4 id="simuladorTitulo"></h4>
        <p id="simuladorTexto" class="muted"></p>
    </div>
</section>

<!-- --- Módulo 3: "5 Minutos BASTAN" --- -->
<section class="section" style="margin-top: 24px;">
    <h2>🕐 5 Minutos BASTAN: Ideas Rápidas</h2>
    <div class="guia-grid" style="margin-top: 16px;">
        <?php foreach ($recetas as $receta): ?>
            <?php
            // Lógica de imagen (ajusta carpetas si es necesario)
            $imgSrc = null;
            if (!empty($receta['imagen'])) {
                if (preg_match('~^https?://~', $receta['imagen'])) { $imgSrc = $receta['imagen']; }
                elseif (isset($receta['user_id']) && $receta['user_id'] !== null && strpos($receta['imagen'], '_') !== false) { $imgSrc = url('assets/img/recetas_usuario/'); }
                else { $imgSrc = url('assets/img/recetas/' . $receta['imagen']); }
            }
            ?>
            <a href="<?= url('/receta?id=' . $receta['id']) ?>" class="guia-card">
                <?php if ($imgSrc): ?><img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($receta['titulo']) ?>"><?php endif; ?>
                <div class="guia-card-content">
                    <h4><?= htmlspecialchars($receta['titulo']) ?></h4>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- --- Módulo 4: Desafío 7 Días --- -->
<section class="section" style="margin-top: 24px; text-align: center;">
    <h2>🔁 Desafío de 7 Días</h2>
    <p class="muted">Intenta desayunar saludable por 7 días seguidos. ¡Registra tu progreso cada día!</p>
    <a href="<?= url('/progreso') ?>" class="btn primary">Ir a mi Check-in de Progreso ✅</a>
</section>


<!-- --- CSS y JS (Añade a main.css o deja aquí) --- -->
<style>
/* Estilos para el simulador */
.simulador-opciones {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    margin-top: 16px;
}
.simulador-btn {
    /* Reutiliza .btn y .ghost, pero con padding/margin ajustados */
    font: inherit;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 14px 18px;
    border-radius: 12px;
    border: 1px solid var(--line);
    background: var(--card);
    color: var(--text);
    cursor: pointer;
    transition: background 0.2s ease, border-color 0.2s ease;
}
.simulador-btn:hover {
    background: var(--panel);
    border-color: var(--brand-2);
}
.simulador-resultado { padding: 16px; }
.simulador-resultado h4 { margin: 0 0 8px 0; }
.simulador-resultado.energia-baja h4 { color: var(--danger); }
.simulador-resultado.energia-media h4 { color: #f59e0b; } /* Amarillo/Naranja */
.simulador-resultado.energia-alta h4 { color: var(--brand); } /* Verde */

/* Estilos para .guia-card (deberías tenerlos de 'guia_nutricional.php') */
.guia-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
.guia-card { display: block; background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-sm); overflow: hidden; transition: all 0.2s ease; }
.guia-card:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
.guia-card img { width: 100%; height: 100px; object-fit: cover; }
.guia-card-content { padding: 12px; }
.guia-card h4 { margin: 0; color: var(--text); font-size: 0.95rem; line-height: 1.4; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const botones = document.querySelectorAll('.simulador-btn');
    const resultadoDiv = document.getElementById('simuladorResultado');
    const tituloRes = document.getElementById('simuladorTitulo');
    const textoRes = document.getElementById('simuladorTexto');

    const impactos = {
        'energia-baja': {
            titulo: 'Crash de Azúcar 📉',
            texto: 'Alto en azúcar refinada. Te dará un pico de energía muy rápido, seguido de un "bajón" que te dejará cansado y con más antojos antes de media mañana.'
        },
        'energia-media': {
            titulo: 'Energía Rápida, pero Incompleta 🤷‍♂️',
            texto: 'Muchos cereales de caja son procesados y altos en azúcar. Es mejor que nada, pero le falta proteína y fibra para mantenerte satisfecho y concentrado.'
        },
        'energia-alta': {
            titulo: '¡Energía Sostenible! 🚀',
            texto: '¡Excelente elección! La proteína y grasa del huevo, más la fibra y carbohidratos de la fruta, te dan energía estable y duradera para toda la mañana.'
        }
    };

    botones.forEach(boton => {
        boton.addEventListener('click', function() {
            const impacto = this.dataset.impacto;
            const data = impactos[impacto];
            
            // Actualizar contenido
            tituloRes.textContent = data.titulo;
            textoRes.textContent = data.texto;

            // Actualizar clases de color
            resultadoDiv.className = 'simulador-resultado card'; // Resetea
            resultadoDiv.classList.add(impacto); // Añade la clase de impacto

            // Mostrar
            resultadoDiv.style.display = 'block';
        });
    });
});
</script>
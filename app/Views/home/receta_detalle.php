<?php
/** @var array $receta */
/** @var array $ingredientes */
/** @var array $nutrientesTotales */
?>

<a href="<?= url('/recetas') ?>" class="link" style="margin-bottom: 24px; display: inline-block;">
    &larr; Volver al recetario
</a>

<div class="receta-header">
    <?php
    // --- LÓGICA COMPLETA PARA DETERMINAR LA RUTA DE LA IMAGEN ---
    $img = $receta['imagen'] ?? null;
    $imgSrc = null;

    if ($img) {
        if (preg_match('~^https?://~', $img)) {
            $imgSrc = $img;
        } elseif (isset($receta['user_id']) && $receta['user_id'] !== null && strpos($img, '_') !== false) {
            $imgSrc = url('assets/img/recetas_usuario/' . $img); // Carpeta de usuario
        } else {
            $imgSrc = url('assets/img/recetas/' . $img); // Carpeta de predefinidas (con la /)
        }
    }
    // --- FIN LÓGICA IMAGEN ---
    ?>

    <?php if ($imgSrc): ?>
        <div class="receta-imagen-hero">
            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($receta['titulo']) ?>">
        </div>
    <?php endif; ?>

    <div class="receta-info card">
        <span class="tag"><?= htmlspecialchars(ucfirst($receta['categoria'] ?? 'General')) ?></span>
        <h1 style="margin-top: 8px; margin-bottom: 0;"><?= htmlspecialchars($receta['titulo']) ?></h1>
        <p class="muted" style="margin-top: 8px;"><?= htmlspecialchars($receta['descripcion'] ?? 'Sin descripción.') ?>
        </p>
    </div>
</div>

<div class="receta-contenido-grid">
    <div class="grid-col-principal">
        <div class="card" style="margin-bottom: 24px;">
            <h3>Ingredientes</h3>

            <?php
            // Verifica si tenemos la lista detallada ($ingredientes viene del controlador)
            if (!empty($ingredientes)):
            ?>
                <ul class="lista-ingredientes-detalle">
                    <?php foreach ($ingredientes as $ing): ?>
                        <li>
                           <span style="font-weight: bold;"><?= htmlspecialchars($ing['cantidad_g']) ?>g</span> -
                           <?= htmlspecialchars($ing['nombre_alimento']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

            <?php
            // Si NO hay detallados, revisa el texto simple ($receta['ingredientes'])
            elseif (!empty($receta['ingredientes'])):
            ?>
                <ul class="lista-ingredientes-texto">
                     <?php foreach (explode("\n", $receta['ingredientes']) as $ingredienteTexto): ?>
                        <?php if(trim($ingredienteTexto)): ?>
                            <li><?= htmlspecialchars(trim($ingredienteTexto)) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <?php /* Puedes quitar esta nota si quieres */ ?>
                <?php /* if (empty($receta['kcal_manual'])): ?>
                    <p class="muted" style="font-size: 0.85em; margin-top: 10px;">(Información nutricional no disponible)</p>
                <?php endif; */ ?>

            <?php
            // Si no hay NADA
            else:
            ?>
                <p class="muted">Esta receta no tiene ingredientes listados.</p>
            <?php endif; ?>
        </div>

        <div class="card" style="margin-bottom: 24px;">
            <h3>Instrucciones</h3>
            <div class="instrucciones-pasos">
                <ol>
                    <?php
                    $instruccionesArray = explode("\n", $receta['instrucciones'] ?? '');
                    foreach ($instruccionesArray as $paso) {
                        $paso = trim($paso);
                        if (!empty($paso)) {
                            echo '<li>' . htmlspecialchars($paso) . '</li>';
                        }
                    }
                    if (empty($instruccionesArray) || (count($instruccionesArray) == 1 && empty($instruccionesArray[0]))) {
                        echo '<li><p class="muted">Sin instrucciones detalladas.</p></li>';
                    }
                    ?>
                </ol>
            </div>
        </div>
    </div>

    <div class="grid-col-sidebar">
        <div class="card" style="margin-bottom: 24px;">
            <h2>Información Nutricional</h2>

            <?php
            // Prioridad 1: Mostrar datos manuales si existen y son válidos
            if (!empty($receta['kcal_manual']) && $receta['kcal_manual'] > 0):
                // Mapeo para nombres amigables
                $nombresNutrientesManual = [
                    'kcal_manual' => 'Calorías',
                    'proteinas_g_manual' => 'Proteínas',
                    'grasas_g_manual' => 'Grasas Totales',
                    'carbos_g_manual' => 'Carbohidratos',
                    'fibra_g_manual' => 'Fibra'
                ];
                $unidadesManual = [ // Unidades para cada campo
                    'kcal_manual' => 'kcal',
                    'proteinas_g_manual' => 'g',
                    'grasas_g_manual' => 'g',
                    'carbos_g_manual' => 'g',
                    'fibra_g_manual' => 'g'
                ];
                ?>
                <p class="muted" style="font-size: 0.9em;">(Valores aproximados por receta completa)</p>
                <div class="nutrientes-grid">
                    <?php foreach ($nombresNutrientesManual as $key => $nombre): ?>
                        <?php if (isset($receta[$key]) && $receta[$key] !== null): ?>
                            <div class="nutriente-item">
                                <strong><?= $nombre ?>:</strong>
                                <span><?= htmlspecialchars($receta[$key]) ?>             <?= $unidadesManual[$key] ?? '' ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <?php
                // Prioridad 2: Mostrar datos calculados si existen y son válidos
            elseif (!empty($nutrientesTotales) && ($nutrientesTotales['Energy']['amount'] ?? 0) > 0):
                // Mapeo de nombres API a nombres amigables
                $nombresNutrientesAPI = [ /* ... (tu array $nombresNutrientes existente) ... */];
                ?>
                <p class="muted" style="font-size: 0.9em;">(Estimación calculada basada en ingredientes FDC)</p>
                <div class="nutrientes-grid">
                    <?php foreach ($nutrientesTotales as $key => $data): ?>
                        <?php if ($data['amount'] > 0 || $key === 'Energy'): ?>
                            <div class="nutriente-item">
                                <strong><?= $nombresNutrientesAPI[$key] ?? $key ?>:</strong>
                                <span><?= $data['amount'] ?>             <?= $data['unit'] ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <?php
                // Si no hay ni manuales ni calculados
            else:
                ?>
                <p class="muted">Información nutricional no disponible para esta receta.</p>
            <?php endif; ?>

        </div>

        <style>
            /* --- Nuevos Estilos para Organización y Imagen --- */

            .receta-header {
                margin-bottom: 24px;
                position: relative;
                /* Para posicionar el card de info encima */
                display: flex;
                /* Para flexbox si no hay imagen */
                flex-direction: column;
                align-items: center;
            }

            .receta-imagen-hero {
    width: 100%;
    /* === AJUSTE CLAVE AQUÍ: Reducir el max-width para que la imagen no sea tan ancha === */
    max-width: 700px; /* Por ejemplo, de 900px a 700px. Experimenta con este valor. */
    /* === OTRA OPCIÓN: Si quieres que la imagen ocupe menos ancho de la pantalla,
         y el texto del título/descripción quede debajo pero también más centrado,
         puedes darle un ancho fijo para el hero, pero mantener la flexibilidad */
    /* width: min(100%, 700px); */ /* Esto asegura que no sea más grande que 700px pero se adapte a pantallas pequeñas */

    /* Aspect ratio de la imagen: Prueba con diferentes valores */
    /* 21 / 9 es muy panorámico (como cine) */
    /* 16 / 9 es estándar de TV/Monitor */
    /* 4 / 3 es más cuadrado */
    /* 3 / 2 es un buen intermedio para fotografía */
    aspect-ratio: 16 / 9; /* Recomiendo 16/9 o 3/2 para este tipo de contenido */
    /* aspect-ratio: 3 / 2; */

    overflow: hidden;
    border-radius: var(--radius);
    margin: 0 auto 24px auto; /* Centra y da margen inferior */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    background-color: var(--card); /* Color de fondo si la imagen no carga */
}

.receta-imagen-hero img {
    width: 100%;
    height: 100%; /* Permite que object-fit cubra todo el espacio del aspect-ratio */
    object-fit: cover; /* Recorta la imagen para que cubra el contenedor sin distorsionarse */
    display: block;
}

            .receta-info {
                width: 100%;
                max-width: 700px;
                /* Ancho máximo para la info (card) */
                text-align: center;
                /* Centrar texto dentro del card */
                padding: 24px;
                background: var(--panel);
                /* Color de fondo */
                /* Si quieres el card de info semi-superpuesto sobre la imagen: */
                /* position: absolute; */
                /* bottom: -30px; */
                /* left: 50%; */
                /* transform: translateX(-50%); */
                /* width: 80%; */
                /* box-shadow: 0 6px 20px rgba(0,0,0,0.3); */
            }

            /* --- Grid para Contenido Principal y Sidebar --- */
            .receta-contenido-grid {
                display: grid;
                gap: 24px;
                /* Espacio entre las columnas */
                margin-top: 24px;
                grid-template-columns: 1fr;
                /* Por defecto, una columna para móviles */
            }

            @media (min-width: 768px) {

                /* A partir de tablets */
                .receta-contenido-grid {
                    grid-template-columns: 2fr 1fr;
                    /* Dos columnas: 2/3 para principal, 1/3 para sidebar */
                }
            }

            /* Estilos de los elementos de las tarjetas */
            .card {
                background: var(--card);
                /* Color de la tarjeta */
                padding: 20px;
                border-radius: var(--radius);
                box-shadow: var(--shadow-sm);
                border: 1px solid var(--line);
            }

            .lista-ingredientes-detalle {
                padding-left: 0;
                list-style: none;
                line-height: 1.7;
            }

            .lista-ingredientes-detalle li {
                padding: 5px 0;
                border-bottom: 1px dashed var(--line-light);
                /* Una línea sutil para separar */
            }

            .lista-ingredientes-detalle li:last-child {
                border-bottom: none;
            }


            .instrucciones-pasos ol {
                padding-left: 20px;
                margin-top: 10px;
            }

            .instrucciones-pasos li {
                margin-bottom: 8px;
                line-height: 1.5;
            }

            .instrucciones-pasos li:last-child {
                margin-bottom: 0;
            }

            /* Estilos para el grid de nutrientes (ya existían, solo para referencia) */
            .nutrientes-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
                /* Más compacto */
                gap: 8px;
                /* Reducir el espacio */
                margin-top: 16px;
            }

            .nutriente-item {
                background: var(--dark-blue-800);
                /* Usa var(--card) si es tu variable general */
                padding: 8px 10px;
                /* Reducir padding */
                border-radius: var(--radius-sm);
                border-left: 3px solid var(--primary);
                font-size: 0.9rem;
                /* Un poco más pequeño */
            }

            .nutriente-item strong {
                display: block;
                color: var(--muted);
                font-size: 0.75rem;
                margin-bottom: 2px;
            }

            .nutriente-item span {
                font-weight: 600;
                font-size: 1rem;
            }
        </style>
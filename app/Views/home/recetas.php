<?php
/** @var array $recetas */
/** @var array $categorias */
/** @var array $filtros */
?>

<h1 style="margin-bottom: 24px;">Recetario</h1>

<div class="layout-sidebar-wrapper">
    
    <aside class="layout-sidebar">
        
        <form method="get" action="<?= url('/recetas') ?>">
            
            <div class="form-group">
                <label for="q">Buscar por nombre</label>
                <input type="text" name="q" id="q" class="input" 
                       placeholder="Ej: Pollo, Avena..." 
                       value="<?= htmlspecialchars($filtros['q'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label>Categorías</label>
                <div class="filter-buttons">
                    <a href="<?= url('/recetas?q=') . htmlspecialchars($filtros['q'] ?? '') ?>"
                       class="btn <?= empty($filtros['categoria']) ? 'primary' : 'ghost' ?>">
                        Todos
                    </a>
                    <?php foreach ($categorias as $cat): ?>
                        <a href="<?= url('/recetas?q=') . htmlspecialchars($filtros['q'] ?? '') . '&categoria=' . urlencode($cat) ?>"
                           class="btn <?= ($filtros['categoria'] ?? '') === $cat ? 'primary' : 'ghost' ?>">
                            <?= htmlspecialchars(ucfirst($cat)) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 20px;">
                <label>Filtros Especiales</label>
                <div class="filter-buttons">
                    
                    <?php
                    $baseUrl = url('/recetas');
                    $queryParams = [];
                    if (!empty($filtros['q'])) $queryParams['q'] = $filtros['q'];
                    if (!empty($filtros['categoria'])) $queryParams['categoria'] = $filtros['categoria'];
                    
                    function toggleFilterUrl($filterName, $currentFilters, $baseUrl, $queryParams) {
                        $newParams = $queryParams;
                        if (!empty($currentFilters[$filterName])) {
                            // Si está activo, lo quitamos
                            // (No hacemos nada aquí, no se añade a $newParams)
                        } else {
                            // Si no está activo, lo añadimos
                            $newParams[$filterName] = '1';
                        }
                        // Usamos http_build_query para manejar correctamente los parámetros
                        return $baseUrl . (empty($newParams) ? '' : '?' . http_build_query($newParams));
                    }
                    ?>

                    <a href="<?= toggleFilterUrl('es_barato', $filtros, $baseUrl, $queryParams) ?>"
                       class="btn <?= !empty($filtros['es_barato']) ? 'primary' : 'ghost' ?>">
                       💰 Económicas
                    </a>
                    
                    <a href="<?= toggleFilterUrl('es_rapido', $filtros, $baseUrl, $queryParams) ?>"
                       class="btn <?= !empty($filtros['es_rapido']) ? 'primary' : 'ghost' ?>">
                       ⏱️ Rápidas
                    </a>
                    
                    <a href="<?= toggleFilterUrl('es_snack_estudio', $filtros, $baseUrl, $queryParams) ?>"
                       class="btn <?= !empty($filtros['es_snack_estudio']) ? 'primary' : 'ghost' ?>">
                       🧠 Snacks Estudio
                    </a>

                </div>
            </div>
            </form> </aside> <main class="layout-content">
        <?php if (!$recetas): ?>
            <div class="card">No se encontraron recetas con esos filtros.</div>
        <?php else: ?>
            <div class="grid cards" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
                
                <?php foreach ($recetas as $receta): ?>
                    <article class="card">
                        <?php
                        $img = $receta['imagen'];
                        if ($img && !preg_match('~^https?://~', $img)) {
                            $img = url('assets/img/' . $img); 
                        }
                        $detalleUrl = url('/receta?id=' . $receta['id']);
                        ?>

                        <?php if (!empty($img)): ?>
                            <a href="<?= htmlspecialchars($detalleUrl) ?>" class="card-image-link">
                                <div style="border-radius:12px;overflow:hidden; aspect-ratio: 16/10; background: #333;">
                                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($receta['titulo']) ?>"
                                         style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            </a>
                        <?php endif; ?>
                        
                        <span class="tag" style="margin-top: 16px;"><?= htmlspecialchars(ucfirst($receta['categoria'] ?? 'General')) ?></span>
                        <h3 style="margin: 8px 0;"><?= htmlspecialchars($receta['titulo']) ?></h3>
                        <p class="muted"><?= htmlspecialchars(mb_strimwidth($receta['descripcion'] ?? '', 0, 100, '…')) ?></p>

                        <a class="link" href="<?= htmlspecialchars($detalleUrl) ?>" style="margin-top: 12px;">Ver más →</a>
                    </article>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>
    </main> </div> ```
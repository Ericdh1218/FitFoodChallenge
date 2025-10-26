<?php
/** @var array $recetas */ // Recibe solo las recetas del usuario
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1>Mis Recetas</h1>
    <a href="<?= url('/recetas/crear') ?>" class="btn primary">
        + Crear Nueva Receta
    </a>
</div>

<?php
// --- Mensaje Flash ---
$mensajeFlash = null;
if (isset($_SESSION['flash_message'])) {
    $mensajeFlash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
?>
<?php if ($mensajeFlash): ?>
    <div class="flash-success card" style="background: #15803d; color: #f0fdf4; margin-bottom: 16px;">
        <?= htmlspecialchars($mensajeFlash) ?>
    </div>
<?php endif; ?>
<?php if (empty($recetas)): ?>
    <div class="card muted">
        Aún no has creado ninguna receta. ¡Anímate a añadir la tuya!
    </div>
<?php else: ?>
    <div class="grid cards" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">

        <?php foreach ($recetas as $receta): ?>
            <article class="card">
                <?php
                // --- LÓGICA PARA DETERMINAR LA RUTA DE LA IMAGEN ---
                $img = $receta['imagen'] ?? null; // Obtiene el nombre del archivo (o null)
                $imgSrc = null; // Variable para la URL final

                if ($img) {
                    // Verifica si es una URL completa (poco probable, pero por si acaso)
                    if (preg_match('~^https?://~', $img)) {
                        $imgSrc = $img;
                    }
                    // Verifica si es una imagen subida por el usuario
                    // Asumimos que tiene user_id y el nombre contiene '_' (userId_timestamp_...)
                    elseif (isset($receta['user_id']) && $receta['user_id'] !== null && strpos($img, '_') !== false) {
                        $imgSrc = url('assets/img/recetas_usuario/' . $img);
                    }
                    // Si no es URL ni de usuario, asumimos que es predefinida
                    else {
                        $imgSrc = url('assets/img/' . $img);
                    }
                }
                // --- FIN LÓGICA IMAGEN ---

                // Enlace al detalle normal de la receta
                $detalleUrl = url('/receta?id=' . $receta['id']);
                ?>

                <?php if ($imgSrc): ?>
                    <a href="<?= htmlspecialchars($detalleUrl) ?>" class="card-image-link">
                        <div style="border-radius:12px;overflow:hidden; aspect-ratio: 16/10; background: #333;">
                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($receta['titulo']) ?>"
                                 style="width:100%;height:100%;object-fit:cover;">
                        </div>
                    </a>
                <?php endif; ?>

                <span class="tag" style="margin-top: 16px;"><?= htmlspecialchars(ucfirst($receta['categoria'] ?? 'General')) ?></span>
                <h3 style="margin: 8px 0;"><?= htmlspecialchars($receta['titulo']) ?></h3>
                <p class="muted"><?= htmlspecialchars(mb_strimwidth($receta['descripcion'] ?? '', 0, 100, '…')) ?></p>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px;">
                   <a class="link" href="<?= htmlspecialchars($detalleUrl) ?>">Ver Detalle</a>
                   </div>
            </article>
        <?php endforeach; ?>

    </div>
<?php endif; ?>
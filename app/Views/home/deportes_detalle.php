<?php 
/** @var array $e */ 

// Determinar a dónde debe volver el enlace
$volverUrl = url('/deportes'); // Por defecto, vuelve a la lista
if (!empty($_GET['return_url'])) {
    $volverUrl = urldecode($_GET['return_url']); // Si viene el parámetro, úsalo
}
?>
<article>
    <a class="link" href="<?= htmlspecialchars($volverUrl) ?>">← Volver</a>
    
    <h1 style="margin-top:8px"><?= htmlspecialchars($e['nombre']) ?></h1>
    <p class="muted">
        <?= htmlspecialchars($e['grupo_muscular'] ?? 'General') ?> •
        <?= htmlspecialchars($e['tipo_entrenamiento'] ?? '—') ?> •
        <?= htmlspecialchars($e['equipamiento'] ?? 'Sin equipo') ?>
    </p>

    <?php
    $img = $e['media_url'];
    if ($img && !preg_match('~^https?://~', $img)) {
        $img = url('assets/img/' . $img);
    }
    ?>
    <?php if (!empty($img)): ?>
        <div style="border-radius:12px;overflow:hidden;margin:16px 0;">
            <img src="<?= htmlspecialchars($img) ?>" alt="Imagen de <?= htmlspecialchars($e['nombre']) ?>" style="width:100%;height:auto;">
        </div>
    <?php endif; ?>

    <div class="card">
        <h3>Descripción</h3>
        <p><?= nl2br(htmlspecialchars($e['descripcion'] ?? '')) ?></p>
    </div>

    <?php if (!empty($e['video_url'])): ?>
        <?php
        /**
         * Función MEJORADA para extraer el ID de un video de YouTube,
         * incluyendo formatos de video normal (watch?v=) y Shorts (shorts/).
         */
        function getYoutubeVideoId(string $url): ?string
        {
            $pattern_normal = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|)([\w-]{11})/';
            $pattern_shorts = '/(?:https?:\/\/)?(?:www\.)?youtube\.com\/shorts\/([\w-]{11})/';

            if (preg_match($pattern_normal, $url, $matches)) {
                return $matches[1]; // Devuelve el ID de video normal
            }
            if (preg_match($pattern_shorts, $url, $matches)) {
                return $matches[1]; // Devuelve el ID de video Short
            }
            
            return null; // No se encontró ningún ID
        }

        // Obtenemos el ID del video
        $videoId = getYoutubeVideoId($e['video_url']);
        ?>

        <?php if ($videoId): ?>
            <h3 style="margin-top: 24px; margin-bottom: 8px;">Video Demostrativo</h3>
            
            <div class="video-container">
                <iframe
                    src="https://www.youtube.com/embed/<?= htmlspecialchars($videoId) ?>"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        <?php else: ?>
            <div class="card" style="margin-top: 16px;">
                <p>
                    <a class="link" target="_blank" rel="noopener" href="<?= htmlspecialchars($e['video_url']) ?>">
                        Ver video demostrativo 🎬
                    </a>
                </p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    </article>
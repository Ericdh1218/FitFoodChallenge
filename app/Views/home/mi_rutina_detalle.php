<?php 
/** @var array $rutina */
/** @var array $ejercicios */ 
?>

<a class="link" href="<?= url('/rutinas') ?>">← Volver a Mis Rutinas</a>
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1 style="margin-top: 8px;"><?= htmlspecialchars($rutina['nombre_rutina']) ?></h1>
    
    <a href="<?= url('/rutina/editar?id=' . $rutina['id'] . '&modo=editar') ?>" class="btn primary">
        ✏️ Editar Rutina
    </a>
</div>

<div style="margin-top: 24px;">
    <h2>Ejercicios de la Rutina</h2>
    
    <?php if (empty($ejercicios)): ?>
        <div class="card muted" style="margin-top: 16px;">
            Esta rutina aún no tiene ejercicios. <a href="<?= url('/rutina/editar?id=' . $rutina['id'] . '&modo=editar') ?>" class="link">Añádelos aquí</a>.
        </div>
    <?php else: ?>
        <table class="table" style="margin-top: 16px;">
            <thead>
                <tr>
                    <th>Ejercicio</th>
                    <th>Series</th>
                    <th>Reps</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ejercicios as $ej): ?>
                    <tr>
                        <td>
                            <?php
                                $returnUrl = urlencode(url('/mi-rutina?id=' . $rutina['id'])); // Volver a esta vista
                                $detalleEjercicioUrl = url('/deportes?id=' . $ej['id'] . '&return_url=' . $returnUrl);
                                $imgSrc = null;
                                if (!empty($ej['media_url'])) {
                                    $img = $ej['media_url'];
                                    if (!preg_match('~^https?://~', $img)) { $imgSrc = url('assets/img/' . $img); } else { $imgSrc = $img; }
                                }
                            ?>
                            <a href="<?= htmlspecialchars($detalleEjercicioUrl) ?>" class="link-ejercicio-tabla"> 
                                <?php if($imgSrc): ?>
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="">
                                <?php else: ?>
                                    <div style="width: 40px; height: 40px; background: var(--card); border-radius: 4px; display: inline-block;"></div>
                                <?php endif; ?>
                                <span><?= htmlspecialchars($ej['nombre']) ?></span>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($ej['series'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($ej['repeticiones'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php
/** @var array $usuarios */
/** @var array|null $miRanking */
/** @var array $insignias */
/** @var array $misBadges */
?>

<h1 style="margin-bottom: 8px;">🏆 Ranking de Usuarios</h1>
<p class="muted">¡Mira quiénes lideran la tabla de puntos de experiencia (XP)!</p>

<?php if ($miRanking): ?>
<div class="section" style="border: 2px solid var(--brand-2); background: var(--panel); margin-top: 24px;">
    <h2>Tu Posición</h2>
    <div class="mi-ranking-card">
        <div class="mi-rank">#<?= htmlspecialchars($miRanking['rank']) ?></div>
        <div class="mi-rank-info">
            <strong>Tú (<?= htmlspecialchars($_SESSION['usuario_nombre']) ?>)</strong>
            <span>Nivel <?= htmlspecialchars($miRanking['level']) ?> • <?= htmlspecialchars($miRanking['xp']) ?> XP</span>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="section" style="margin-top: 24px;">
    <h2>Top 10 Usuarios</h2>
    <ol class="ranking-lista">
        <?php if (empty($usuarios)): ?>
            <p class="muted">El ranking aún está vacío.</p>
        <?php else: ?>
            <?php foreach ($usuarios as $index => $usuario): ?>
                <li class="ranking-item rank-<?= $index + 1 ?>">
                    <span class="rank-num">#<?= $index + 1 ?></span>
                    <span class="rank-nombre"><?= htmlspecialchars($usuario['nombre']) ?></span>
                    <span class="rank-xp">Nvl <?= htmlspecialchars($usuario['level']) ?> • <?= htmlspecialchars($usuario['xp']) ?> XP</span>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ol>
</div>
<div class="section" style="margin-top: 32px;">
    <h2>Insignias y cómo conseguirlas</h2>
    <p class="muted" style="margin-top:6px;">
        Completa retos y acciones para desbloquear insignias y ganar XP adicional.
    </p>

    <?php if (empty($insignias)): ?>
        <div class="card muted" style="margin-top:12px;">Aún no hay insignias definidas.</div>
    <?php else: ?>
        <div class="badges-grid">
            <?php foreach ($insignias as $b): 
                $codigo = $b['codigo'] ?? '';
                $tiene  = in_array($codigo, $misBadges ?? [], true);
                $icono  = $b['icono_url'] ?? 'insignia_generica.png';
                if (!preg_match('~^https?://~', $icono)) {
                    $icono = url('assets/img/iconos_insignias/' . $icono);
                }
            ?>
            <div class="badge-card <?= $tiene ? 'badge-ok' : 'badge-lock' ?>">
                <div class="badge-thumb">
                    <img src="<?= htmlspecialchars($icono) ?>" alt="<?= htmlspecialchars($b['nombre'] ?? 'Insignia') ?>">
                    <?php if ($tiene): ?>
                        <span class="badge-state ok">✔</span>
                    <?php else: ?>
                        <span class="badge-state lock">🔒</span>
                    <?php endif; ?>
                </div>
                <div class="badge-info">
                    <strong class="badge-title"><?= htmlspecialchars($b['nombre'] ?? 'Insignia') ?></strong>
                    <p class="badge-desc">
                        <?= htmlspecialchars($b['descripcion'] ?? 'Completa la acción para desbloquearla.') ?>
                    </p>
                    <span class="badge-xp">+<?= (int)($b['xp_recompensa'] ?? 0) ?> XP</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.mi-ranking-card { 
    display: flex; 
    align-items: center; 
    gap: 20px; 
    margin-top: 16px;
}
.mi-rank {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--brand-2);
    line-height: 1;
}
.mi-rank-info strong { 
    display: block; 
    font-size: 1.2rem; 
    color: var(--text); 
}
.mi-rank-info span { color: var(--muted); }

.ranking-lista { 
    list-style: none; 
    padding: 0; 
    margin: 16px 0 0 0; 
}
.ranking-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 16px;
    background: var(--card);
    border-radius: var(--radius-sm);
    margin-bottom: 10px;
    border: 1px solid var(--line);
    transition: transform 0.2s ease, border-color 0.2s ease;
}
.ranking-item:hover {
    transform: translateY(-2px);
}

/* Estilos para el Top 3 */
.ranking-item.rank-1 { 
    background: linear-gradient(90deg, rgba(245, 158, 11, 0.2), var(--card) 60%); 
    border-color: #f59e0b; /* Oro */
}
.ranking-item.rank-2 { 
    background: linear-gradient(90deg, rgba(168, 162, 158, 0.2), var(--card) 60%); 
    border-color: #a8a29e; /* Plata */
}
.ranking-item.rank-3 { 
    background: linear-gradient(90deg, rgba(205, 104, 2, 0.2), var(--card) 60%); 
    border-color: #cd7f32; /* Bronce */
}

.rank-num {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--muted);
    width: 30px;
}
.ranking-item.rank-1 .rank-num,
.ranking-item.rank-2 .rank-num,
.ranking-item.rank-3 .rank-num {
    color: var(--text);
}

.rank-nombre {
    font-weight: 600;
    color: var(--text);
    flex-grow: 1; /* Ocupa el espacio del medio */
}
.rank-xp {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--brand); /* XP en verde */
}
.badges-grid{
  display:grid;
  grid-template-columns: repeat(auto-fill, minmax(220px,1fr));
  gap:14px; margin-top:12px;
}
.badge-card{
  display:grid; grid-template-columns: 72px 1fr; gap:12px;
  background: var(--card); border:1px solid var(--line);
  border-radius: var(--radius-sm); padding:12px;
}
.badge-thumb{ position:relative; width:72px; height:72px; border-radius:12px; overflow:hidden; background:#0f172a; }
.badge-thumb img{ width:100%; height:100%; object-fit:contain; }
.badge-state{
  position:absolute; right:6px; bottom:6px; font-size:0.9rem;
  border-radius:999px; padding:2px 6px; line-height:1;
}
.badge-state.ok{ background:#22c55e; color:#07151f; font-weight:800; }
.badge-state.lock{ background:#334155; color:#e2e8f0; }

.badge-info .badge-title{ color:var(--text); }
.badge-info .badge-desc{ color:var(--muted); font-size:0.9rem; margin:4px 0 6px; min-height:2.6em; }
.badge-xp{
  display:inline-block; font-weight:700; color:#22c55e; background:rgba(34,197,94,.1);
  border:1px solid #22c55e33; padding:2px 8px; border-radius:999px; font-size:0.85rem;
}

/* estados visuales */
.badge-card.badge-lock{ opacity:.8; }
.badge-card.badge-lock .badge-thumb{ filter:saturate(.4); }
</style>
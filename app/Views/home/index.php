<?php
/** @var array $stats */
$racha = (int)($stats['racha'] ?? 0);
$min   = (int)($stats['min_actividad'] ?? 0);
$agua  = (int)($stats['vasos_agua'] ?? 0);
?>
<section class="hero">
  <div class="hero-text">
    <h1>Actívate hoy. <span class="text-brand">Cuida tu cuerpo</span> y tu mente.</h1>
    <p>Mini retos diarios de movimiento y hábitos saludables...</p>
    <div class="quick-actions">
      <a href="<?= url('/deportes') ?>" class="btn primary">Explorar actividades</a>
      <a href="<?= url('/habitos') ?>" class="btn ghost">Ver hábitos</a>

      <!-- Botones rápidos (OJO: type="button") -->
      <button id="btnAddMin"  type="button" class="qa-btn" aria-label="Sumar 5 minutos">+5 min</button>
      <button id="btnAddAgua" type="button" class="qa-btn" aria-label="Sumar 1 vaso de agua">+1 vaso</button>
    </div>
  </div>

  <div class="hero-stats">
    <div class="stat"><strong id="statRacha"><?= $racha ?></strong> <span>días de racha</span></div>
    <div class="stat"><strong id="statMin"><?= $min ?></strong> <span>min de actividad</span></div>
    <div class="stat"><strong id="statAgua"><?= $agua ?></strong> <span>vasos de agua</span></div>
  </div>
</section>

<div class="grid cards" style="margin-top: 28px;">

    <article class="card">
        <h3>Reto 7x7</h3>
        <p>7 minutos de actividad durante 7 días seguidos. ¡Empieza hoy!</p>
        <a href="<?= url('/desafios/ver?codigo=reto-7x7') ?>" class="link">Comenzar →</a> 
    </article>

    <article class="card">
        <h3>Guia nutricional</h3>
        <p>Aprende lo basico de como mejorar tu alimentacion.</p>
        <a href="<?= url('/guia-nutricional') ?>" class="link">Ver guía →</a> 
    </article>

    <article class="card">
        <h3>Mi Progreso</h3>
        <p>Lleva el registro de tu actividad física y hábitos diarios.</p>
        <a href="<?= url('/progreso') ?>" class="link">Abrir panel →</a> 
    </article>

    <article class="card">
        <h3>Ranking e Insignias</h3>
        <p>Desbloquea insignias por mantenerte activo y realizar mejoras en tus habitos y se el mejor del ranking.</p>
        <a href="<?= url('/ranking') ?>" class="link">Ver ranking e insignias →</a> 
    </article>
    
    <article class="card">
        <h3>Reto de agua</h3>
        <p>Bebe al menos 6 vasos de agua diarios y mejora tu energía.</p>
        <a href="<?= url('/desafios/ver?codigo=reto-agua') ?>" class="link">Unirme al reto →</a>
    </article>

    <article class="card">
        <h3>Desayuno saludable</h3>
        <p>Ideas simples para comenzar tu día con proteína y fruta.</p>
        <a href="<?= url('/habitos/plan?area=desayuno') ?>" class="link">Ver ideas →</a> 
    </article>
    
</div>

<section class="card" style="margin-top:40px">
  <h2 style="margin-top:0">¿Qué es FitFoodChallenge?</h2>
  <p>Es una iniciativa para promover la actividad física y los buenos hábitos alimenticios entre jóvenes como tú. Cada día puedes registrar tus avances, mantener una racha, aprender con mini retos y ver cómo mejoras paso a paso.</p>
  <p>Todo es gratuito, sin necesidad de equipo, y lo puedes hacer desde casa o donde estés.</p>
</section>

<style>
  /* Quick actions del hero */
  .quick-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
  .quick-actions .qa-btn {
    padding: 10px 14px;
    border-radius: 999px;
    border: 1px solid var(--line);
    background: var(--card);
    color: var(--text);
    font-weight: 600;
    transition: transform .06s ease, background .2s ease, border-color .2s ease;
  }
  .quick-actions .qa-btn:hover { transform: translateY(-1px); background: #1f2733; border-color: #2b3a4e; }
  .quick-actions .qa-btn:active { transform: translateY(0); }
  .hero .stat strong { font-size: 28px; line-height: 1; }
  .hero .stat span { display:block; font-size: 12px; color: var(--muted); margin-top: 6px; }
  .toast {
    position: fixed; right: 16px; bottom: 16px;
    background: #1e293b; color: #fff; border:1px solid #334155;
    padding: 10px 12px; border-radius: 10px; font-size: 14px; z-index: 9999;
  }
</style>

<script>
const $racha = document.getElementById('statRacha');
const $min   = document.getElementById('statMin');
const $agua  = document.getElementById('statAgua');

function toast(msg, ok=true){
  const el = document.createElement('div');
  el.className = 'toast';
  el.style.background = ok ? '#14532d' : '#7f1d1d';
  el.style.borderColor = ok ? '#166534' : '#991b1b';
  el.textContent = msg;
  document.body.appendChild(el);
  setTimeout(()=> el.remove(), 2500);
}

function refreshUi(data){
  if (!data || !data.ok) return;
  if (data.racha != null) $racha.textContent = data.racha;
  if (data.hoy) {
    if (data.hoy.min_actividad != null) $min.textContent  = data.hoy.min_actividad;
    if (data.hoy.vasos_agua    != null) $agua.textContent = data.hoy.vasos_agua;
  }
}

async function postForm(url, payload) {
  const form = new URLSearchParams();
  for (const k in payload) form.append(k, payload[k]);
  const res = await fetch(url, {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: form.toString(),
    credentials: 'same-origin' // asegura envío de cookies/sesión
  });
  const text = await res.text();
  try { return JSON.parse(text); }
  catch { 
    console.error('Respuesta no-JSON de', url, text);
    return { ok:false, error:'not-json', raw:text, status:res.status };
  }
}

// Carga inicial (si quieres refrescar con el valor real del server).
fetch('<?= url("/progreso/stats") ?>', {credentials:'same-origin'})
  .then(r => r.text())
  .then(t => { try { refreshUi(JSON.parse(t)); } catch(e){ console.error('stats no JSON', t); } })
  .catch(()=>{});

document.getElementById('btnAddMin')?.addEventListener('click', async () => {
  const data = await postForm('<?= url("/progreso/add-minutos") ?>', {delta: 5});
  if (!data.ok) { 
    toast(data.status === 401 ? 'Inicia sesión para usar los botones' : 'No se pudo actualizar', false);
    return;
  }
  refreshUi(data);
  toast('¡+5 min añadidos!');
});

document.getElementById('btnAddAgua')?.addEventListener('click', async () => {
  const data = await postForm('<?= url("/progreso/add-agua") ?>', {delta: 1});
  if (!data.ok) { 
    toast(data.status === 401 ? 'Inicia sesión para usar los botones' : 'No se pudo actualizar', false);
    return;
  }
  refreshUi(data);
  toast('¡+1 vaso de agua!');
});
</script>
<section class="hero">
    <div class="hero-text">
        <h1>Actívate hoy. <span class="text-brand">Cuida tu cuerpo</span> y tu mente.</h1>
        <p>Mini retos diarios de movimiento y hábitos saludables...</p>
        <div>
            <a href="<?= url('/deportes') ?>" class="btn primary">Explorar actividades</a> 
            <a href="<?= url('/habitos') ?>" class="btn ghost">Ver hábitos</a> 
        </div>
    </div>
    <div class="hero-stats">
        <div class="stat"><strong>0</strong> <span>días de racha</span></div>
        <div class="stat"><strong>0</strong> <span>min de actividad</span></div>
        <div class="stat"><strong>0</strong> <span>vasos de agua</span></div>
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

<?php /** @var array $terminosAgrupados */ ?>

<h1>Glosario Fit</h1>
<p class="muted">Conceptos clave de nutrición y ejercicio explicados fácil.</p>
<form method="get" class="filtro-form" style="margin-top: 16px;">
  <label for="categoria">Filtrar por categoría:</label>
  <select name="categoria" id="categoria" onchange="this.form.submit()" style="margin-left:8px;">
      <option value="">Todas</option>
      <option value="Nutrición" <?= ($_GET['categoria'] ?? '')==='Nutrición' ? 'selected' : '' ?>>Nutrición</option>
      <option value="Ejercicio" <?= ($_GET['categoria'] ?? '')==='Ejercicio' ? 'selected' : '' ?>>Ejercicio</option>
      <option value="Suplementación" <?= ($_GET['categoria'] ?? '')==='Suplementación' ? 'selected' : '' ?>>Suplementación</option>
      <option value="Bienestar" <?= ($_GET['categoria'] ?? '')==='Bienestar' ? 'selected' : '' ?>>Bienestar</option>
      <option value="General" <?= ($_GET['categoria'] ?? '')==='General' ? 'selected' : '' ?>>General</option>
  </select>
</form>

<div style="margin: 24px 0;">
        <a href="<?= url('/glosario/quiz') ?>" class="btn primary">
    🧠 ¡Ponte a prueba! (Quiz Aleatorio)
</a>
    </div>
<div class="glosario-container" style="margin-top: 24px;">
    <?php foreach ($terminosAgrupados as $letra => $terminos): ?>
        <section class="glosario-letra-section">
            <h2><?= htmlspecialchars($letra) ?></h2>
            <?php foreach ($terminos as $t): ?>
                <div class="glosario-termino card">
                    <h3><?= htmlspecialchars($t['termino']) ?></h3>
                    <p class="muted"><?= nl2br(htmlspecialchars($t['definicion'])) ?></p>
                    <?php if(!empty($t['categoria'])): ?>
                        <span class="tag" style="margin-top: 8px;"><?= htmlspecialchars($t['categoria']) ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>


</div>

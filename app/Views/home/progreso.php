<section class="section">
  <h2 style="margin-top:0">Registrar avance</h2>
  <form id="form-progress" method="post" action="<?= url('/progreso') ?>" class="grid" style="grid-template-columns:repeat(3,1fr);gap:12px;">
    <input class="input" type="number" name="minutes" min="0" max="600" placeholder="Minutos (ej. 10)">
    <input class="input" type="number" name="water"   min="0" max="30"  placeholder="Vasos de agua (ej. 6)">
    <button class="btn primary" style="grid-column: span 3;">Guardar</button>
  </form>
  <p class="muted" id="saveMsg"></p>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('form-progress');
  if (!form) return;
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(form);
    const res = await fetch('<?= url('/progreso') ?>', { method: 'POST', body: fd });
    const data = await res.json().catch(()=>({ok:false}));
    const msg = document.getElementById('saveMsg');
    if (data.ok) { msg.textContent = 'Guardado ✅'; msg.style.color = '#22c55e'; }
    else { msg.textContent = 'Error ❌'; msg.style.color = '#ef4444'; }
  });
});
</script>

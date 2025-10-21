// assets/js/main.js
// Simple client-side counters (placeholder until DB integration)
const $ = (sel) => document.querySelector(sel);

function loadCounters(){
  const data = JSON.parse(localStorage.getItem('js_counters') || '{"streak":0,"minutes":0,"water":0}');
  const s = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
  s('streak', data.streak);
  s('minutes', data.minutes);
  s('water', data.water);
}

// toggle del menú móvil
const btn = document.querySelector('.menu-toggle');
const nav = document.querySelector('.nav[data-collapsible]');

if (btn && nav) {
  btn.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
}

// Filtro por categoría en /deportes
document.querySelectorAll('#filtros [data-filter]').forEach(btn => {
  btn.addEventListener('click', () => {
    const tag = btn.getAttribute('data-filter');
    const cards = document.querySelectorAll('[data-workout-list] .card');

    cards.forEach(card => {
      const tags = (card.getAttribute('data-tags') || '').split(',');
      const show = tag === 'all' || tags.includes(tag);
      card.style.display = show ? '' : 'none';
    });
  });
});


document.addEventListener('DOMContentLoaded', loadCounters);
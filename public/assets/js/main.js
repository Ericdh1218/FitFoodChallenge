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

// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
    // Selecciona TODOS los formularios de eliminación
    const deleteForms = document.querySelectorAll('.delete-form');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            // 1. Previene el envío normal del formulario
            event.preventDefault(); 

            // 2. Muestra el modal de SweetAlert2
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning', // Icono de advertencia
                showCancelButton: true, // Muestra el botón de cancelar
                confirmButtonColor: '#d33', // Color rojo para confirmar
                cancelButtonColor: '#3085d6', // Color azul para cancelar
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                // Estilos para tema oscuro (opcional pero recomendado)
                background: 'var(--panel, #0f172a)', 
                color: 'var(--text, #e5e7eb)',
            }).then((result) => {
                // 3. Si el usuario confirma...
                if (result.isConfirmed) {
                    // ...envía el formulario original de forma programática
                    form.submit(); 
                }
            });
        });
    });

    // Opcional: Estilo específico para el botón Eliminar
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.style.color = 'var(--danger, #ef4444)';
        button.style.borderColor = 'var(--danger, #ef4444)';
    });

});
document.addEventListener('DOMContentLoaded', loadCounters);
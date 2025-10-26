<h1>Crear Nueva Receta</h1>
<p class="muted">Añade tu propia receta a la biblioteca.</p>

<form action="<?= url('/recetas/crear') ?>" method="POST" enctype="multipart/form-data" style="margin-top: 24px;">

    <div class="form-group">
        <label for="imagen_receta">Imagen de la Receta (Opcional)</label>
        <input type="file" id="imagen_receta" name="imagen_receta" class="input" accept="image/jpeg, image/png, image/gif">
        <small class="muted">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB.</small>
    </div>
    <div class="form-group">
        <label for="titulo">Nombre de la Receta</label>
        <input type="text" id="titulo" name="titulo" class="input" required>
    </div>

    <div class="form-group">
        <label for="descripcion">Descripción Corta</label>
        <textarea id="descripcion" name="descripcion" class="input" rows="3"></textarea>
    </div>

    <div class="form-group">
        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria" class="input">
            <option value="comida de descanso">Comida de Descanso</option>
            <option value="pre entreno">Pre Entreno</option>
            <option value="post entreno">Post Entreno</option>
            <option value="intra entreno">Intra Entreno</option>
            <option value="antes de dormir">Antes de Dormir</option>
        </select>
    </div>

    <div class="section" style="margin-top: 24px;">
        <h2>Ingredientes</h2>

        <h2>Buscar Ingredientes / Alimentos</h2>
<input type="text" id="foodSearchInput" class="input" placeholder="Buscar alimento (ej: Manzana, Pollo)...">
<div id="foodSearchResults" style="margin-top: 10px; max-height: 300px; overflow-y: auto;">
    </div>

        <h3 style="margin-top: 24px;">Ingredientes Añadidos:</h3>
        <ul id="ingredientList" class="lista-ingredientes-receta">
            <li id="noIngredientsMsg" class="muted">Aún no has añadido ingredientes.</li>
        </ul>
        <input type="hidden" name="ingredientes_fdc" id="ingredientesFdcInput">

    </div> <div class="form-group" style="margin-top: 24px;">
        <label for="instrucciones">Instrucciones</label>
        <textarea id="instrucciones" name="instrucciones" class="input" rows="6" placeholder="Escribe los pasos aquí..."></textarea>
    </div>

    <button type="submit" class="btn primary" style="width: 100%; margin-top: 24px;">Guardar Receta</button>

</form>

<style>
.lista-ingredientes-receta { list-style: none; padding: 0; margin-top: 10px; }
.lista-ingredientes-receta li {
    display: flex; justify-content: space-between; align-items: center;
    background: var(--card); padding: 8px 12px; border-radius: var(--radius-sm);
    margin-bottom: 8px; border: 1px solid var(--line); font-size: 0.95rem;
}
.lista-ingredientes-receta .cantidad-input { width: 60px; padding: 4px 8px; font-size: 0.9rem; margin-right: 10px; }
.lista-ingredientes-receta .btn-quitar-ing { background: none; border: none; color: var(--danger); cursor: pointer; padding: 4px;}

/* Estilos para resultados de búsqueda (igual que antes) */
.search-result-item { /* ... */ }
.search-result-item:hover { /* ... */ }
.search-result-item strong { /* ... */ }
.search-result-item .btn-sm { /* ... */ }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('foodSearchInput');
    const resultsDiv = document.getElementById('foodSearchResults');
    const ingredientList = document.getElementById('ingredientList');
    const noIngredientsMsg = document.getElementById('noIngredientsMsg');
    const ingredientesFdcInput = document.getElementById('ingredientesFdcInput');
    let searchTimeout;
    let addedIngredients = {}; // Objeto para guardar { fdcId: { name: '...', amount: '...' } }

    // --- Función de Búsqueda AJAX ---
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.trim();
        clearTimeout(searchTimeout);

        if (query.length < 3) {
            resultsDiv.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(async () => {
            resultsDiv.innerHTML = '<p class="muted">Buscando...</p>';
            try {
                const response = await fetch(`<?= url('/alimentos/buscar') ?>?q=${encodeURIComponent(query)}`);
                if (!response.ok) { throw new Error('Error en la respuesta del servidor'); }
                const data = await response.json();

                if (data.success && data.foods && data.foods.length > 0) {
                    resultsDiv.innerHTML = ''; // Limpiar
                    data.foods.forEach(food => {
                        const foodElement = document.createElement('div');
                        foodElement.classList.add('search-result-item', 'card');
                        let brandInfo = food.brandOwner ? ` (${food.brandOwner})` : '';
                        foodElement.innerHTML = `
                            <div>
                                <strong>${escapeHtml(food.description)}</strong>
                                <small class="muted">${escapeHtml(brandInfo)}</small>
                            </div>
                            <button type="button" class="btn btn-sm primary btn-select-food" data-fdc-id="${food.fdcId}" data-food-name="${escapeHtml(food.description)}">Seleccionar</button>
                        `;
                        resultsDiv.appendChild(foodElement);
                    });
                } else if (data.success) {
                    resultsDiv.innerHTML = '<p class="muted">No se encontraron alimentos.</p>';
                } else {
                    resultsDiv.innerHTML = `<p class="muted" style="color:var(--danger);">${escapeHtml(data.message) || 'Error desconocido.'}</p>`;
                }
            } catch (error) {
                console.error('Error fetching food data:', error);
                resultsDiv.innerHTML = '<p class="muted" style="color:var(--danger);">Error al conectar. Intenta de nuevo.</p>';
            }
        }, 500);
    });

    // --- Función para AÑADIR Ingrediente a la lista ---
    resultsDiv.addEventListener('click', function(event) {
        if (event.target.classList.contains('btn-select-food')) {
            const fdcId = event.target.dataset.fdcId;
            const foodName = event.target.dataset.foodName;
            if (addedIngredients[fdcId]) {
                // Asume que SweetAlert está cargado
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'Duplicado', text: `${foodName} ya ha sido añadido.`, icon: 'info', background: 'var(--panel)', color: 'var(--text)' });
                } else {
                    alert(`${foodName} ya ha sido añadido.`);
                }
                return;
            }
            addedIngredients[fdcId] = { name: foodName, amount: 100 };
            renderIngredientList();
            updateHiddenInput();
            resultsDiv.innerHTML = '';
            searchInput.value = '';
            searchInput.focus();
        }
    });

    // --- Función para QUITAR Ingrediente de la lista ---
    ingredientList.addEventListener('click', function(event) {
        if (event.target.classList.contains('btn-quitar-ing')) {
            const fdcId = event.target.dataset.fdcId;
            delete addedIngredients[fdcId];
            renderIngredientList();
            updateHiddenInput();
        }
    });

    // --- Función para actualizar cantidad ---
     ingredientList.addEventListener('input', function(event) {
        if (event.target.classList.contains('cantidad-input')) {
            const fdcId = event.target.dataset.fdcId;
            if (addedIngredients[fdcId]) { // Asegura que exista
                 addedIngredients[fdcId].amount = event.target.value;
                 updateHiddenInput();
            }
        }
    });

    // ==========================================================
    // === ¡FUNCIONES FALTANTES AÑADIDAS AQUÍ! ===
    // ==========================================================

    // --- Función para renderizar la lista de ingredientes ---
    function renderIngredientList() {
        if (!ingredientList) return; // Salir si el elemento no existe
        ingredientList.innerHTML = ''; // Limpia la lista actual
        const hasIngredients = Object.keys(addedIngredients).length > 0;

        if(noIngredientsMsg) noIngredientsMsg.style.display = hasIngredients ? 'none' : 'block'; // Muestra/oculta mensaje

        if (hasIngredients) {
            for (const fdcId in addedIngredients) {
                const ingredient = addedIngredients[fdcId];
                const li = document.createElement('li');
                li.innerHTML = `
                    <span>
                        <input type="number" class="input cantidad-input" data-fdc-id="${fdcId}" value="${ingredient.amount}" step="1" min="1"> g -
                        ${escapeHtml(ingredient.name)}
                    </span>
                    <button type="button" class="btn-quitar-ing" data-fdc-id="${fdcId}">&times;</button>
                `;
                ingredientList.appendChild(li);
            }
        }
    }

    // --- Función para actualizar el input oculto ---
    function updateHiddenInput() {
        if (!ingredientesFdcInput) return; // Salir si el elemento no existe
        // Guarda los datos como JSON stringificado
        ingredientesFdcInput.value = JSON.stringify(addedIngredients);
    }

    // --- Helper escapeHtml ---
    function escapeHtml(unsafe) {
        if (typeof unsafe !== 'string') return ''; // Asegura que sea string
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }
    // ==========================================================
    // === FIN FUNCIONES FALTANTES ===
    // ==========================================================


    // Render inicial
    renderIngredientList();

}); // Fin DOMContentLoaded
</script>
<style>
.search-result-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 10px;
    border-bottom: 1px solid var(--line);
    cursor: pointer;
}
.search-result-item:hover {
    background-color: var(--card);
}
.search-result-item strong {
    margin-right: 10px;
}
.search-result-item .btn-sm {
     margin: 0; /* Quita margen del botón */
     flex-shrink: 0;
}
</style>
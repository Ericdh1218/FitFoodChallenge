<div class="auth-card-wrapper">
    <div class="auth-card">

        <h1 class="auth-title">¡Bienvenido/a! Un último paso</h1>
        <p class="auth-subtitle">Ayúdanos a personalizar tu experiencia en FitFood Challenge.</p>

        <form action="<?= url('/habitos-form') ?>" method="POST" style="margin-top: 24px;">

            <div class="form-group">
                <label for="nivel_actividad">1. ¿Cuál es tu nivel de actividad física semanal?</label>
                <select id="nivel_actividad" name="nivel_actividad" class="input">
                    <option value="sedentario">Sedentario (Poco o ningún ejercicio)</option>
                    <option value="ligero">Ligero (Ejercicio 1-2 días/semana)</option>
                    <option value="activo">Activo (Ejercicio 3-5 días/semana)</option>
                    <option value="muy_activo">Muy Activo (Ejercicio 6-7 días/semana)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="objetivo_principal">2. ¿Cuál es tu objetivo principal?</label>
                <input type="text" id="objetivo_principal" name="objetivo_principal" class="input"
                    placeholder="Ej: Perder 5 kg, correr 10k, etc." required>
            </div>

            <div class="form-group">
                <label for="nivel_alimentacion">3. ¿Cómo describirías tu relación con la comida?</label>
                <select id="nivel_alimentacion" name="nivel_alimentacion" class="input">
                    <option value="novato">Novato (Aún no le presto mucha atención)</option>
                    <option value="aprendiendo">Aprendiendo (Intento comer mejor)</option>
                    <option value="consciente">Consciente (Sé lo que como la mayoría del tiempo)</option>
                    <option value="autonomo">Autónomo (Tengo control total de mi dieta)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="horas_sueno">4. ¿Cuántas horas duermes en promedio por noche?</label>
                <input type="number" id="horas_sueno" name="horas_sueno" class="input" placeholder="Ej: 8" step="0.5"
                    required>
            </div>

            <div class="form-group">
                <label for="consumo_agua">5. ¿Cuántos vasos de agua (250ml) bebes al día?</label>
                <input type="number" id="consumo_agua" name="consumo_agua" class="input" placeholder="Ej: 8" required>
            </div>

            <form action="<?= url('/habitos-form') ?>" method="POST" style="margin-top: 24px;">

                <div class="form-group">
                    <label for="peso">6. ¿Cuál es tu peso actual? (en kg)</label>
                    <input type="number" id="peso" name="peso" class="input" placeholder="Ej: 70.5" step="0.1" required>
                </div>

                <div class="form-group">
                    <label for="altura">7. ¿Cuál es tu altura? (en cm)</label>
                    <input type="number" id="altura" name="altura" class="input" placeholder="Ej: 175" required>
                </div>
                <div class="form-group">
                 <label for="genero">8. ¿Cuál es tu género?</label>
                 <select id="genero" name="genero" class="input" required>
                     <option value="" disabled selected>Selecciona</option>
                     <option value="masculino">Masculino</option>
                     <option value="femenino">Femenino</option>
                 </select>
             </div>
                <button type="submit" class="btn primary" style="width:100%; ...">
                    ¡Comenzar mi reto!
                </button>
            </form>
        </div>
    
</div>
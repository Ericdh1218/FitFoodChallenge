<?php /** @var array $usuario */ ?>

<div class="auth-card-wrapper">
    <div class="auth-card">
        <h1 class="auth-title">Mi Perfil</h1>
        <div class="profile-info">
            <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre'] ?? 'N/A') ?></p>
            <p><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo'] ?? 'N/A') ?></p>

            <p><strong>Edad:</strong> <?= htmlspecialchars($usuario['Edad'] ?? 'N/A') ?> años</p>
            <p><strong>Género:</strong> <?= htmlspecialchars(ucfirst($usuario['genero'] ?? 'N/A')) ?></p>
            <p><strong>Objetivo:</strong> <?= htmlspecialchars($usuario['objetivo_principal'] ?? 'N/A') ?></p>
            <p><strong>Nivel Actividad:</strong> <?= htmlspecialchars(ucfirst($usuario['nivel_actividad'] ?? 'N/A')) ?>
            </p>
            <p><strong>Peso Actual:</strong> <?= htmlspecialchars($usuario['peso'] ?? 'N/A') ?> kg</p>
            <p><strong>Altura:</strong> <?= htmlspecialchars($usuario['altura'] ?? 'N/A') ?> cm</p>
            <p><strong>IMC:</strong> <?= htmlspecialchars($usuario['imc'] ?? 'N/A') ?></p>
        </div>

        <h2 class="auth-title" style="font-size: 24px;">Registrar Progreso</h2>
        <p class="auth-subtitle">Actualiza tu peso para ver tu evolución.</p>

        <form action="<?= url('/progreso') ?>" method="POST" style="margin-top: 24px;">
            <div class="form-group">
                <label for="peso_nuevo">Mi nuevo peso (en kg)</label>
                <input type="number" id="peso_nuevo" name="peso" class="input" placeholder="Ej: 69.5" step="0.1"
                    required>
            </div>

            <button type="submit" class="btn primary" style="width:100%; margin-top:16px;">
                Guardar Nuevo Peso
            </button>
        </form>
    </div>
</div>
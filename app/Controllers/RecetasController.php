<?php
namespace App\Controllers;

use App\Services\FdcApiService;
use App\Models\Receta;
use App\Models\User;
class RecetasController
{
    /**
     * Muestra la lista de recetas (con filtros)
     * Responde a: /recetas
     */
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $model = new Receta();
        
        // === CORRECCIÓN AQUÍ ===
        // Añadimos los nuevos filtros a la lista que recogemos de $_GET
        $filtros = [
            'q'                => $_GET['q'] ?? null,
            'categoria'        => $_GET['categoria'] ?? null,
            'es_barato'        => $_GET['es_barato'] ?? null, // Nuevo
            'es_rapido'        => $_GET['es_rapido'] ?? null, // Nuevo
            'es_snack_estudio' => $_GET['es_snack_estudio'] ?? null, // Nuevo
        ];
        // =======================

        // El modelo ya sabe qué hacer con estos filtros si no son null
        $recetas = $model->all($filtros); 
        $categorias = $model->getCategorias();

        view('home/recetas', [
            'title'      => 'Recetario',
            'recetas'    => $recetas,
            'categorias' => $categorias,
            'filtros'    => $filtros // Pasamos todos los filtros a la vista
        ]);
    }

    /**
     * Muestra el detalle de UNA receta
     * Responde a: /receta?id=...
     */
    /**
     * Muestra el detalle de UNA receta, calculando sus nutrientes totales.
     * Responde a: /receta?id=...
     */
    public function show()
    {
        if (!isset($_SESSION['usuario_id'])) { /* ... login ... */ }
        $userId = $_SESSION['usuario_id']; // No se usa aquí, pero bueno tenerlo

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { /* ... redirect ... */ }

        $recetaModel = new Receta();
        $fdcService = new FdcApiService(); // Instancia el servicio FDC

        // 1. Obtener datos básicos de la receta
        $receta = $recetaModel->find($id);
        if (!$receta) { /* ... redirect ... */ }

        // 2. Obtener los ingredientes de la receta (fdc_id, cantidad_g)
        $ingredientes = $recetaModel->getIngredientsForRecipe($id);

        $nutrientesTotales = [
            'Energy' => ['amount' => 0, 'unit' => 'kcal'],
            'Protein' => ['amount' => 0, 'unit' => 'g'],
            'Total lipid (fat)' => ['amount' => 0, 'unit' => 'g'],
            'Carbohydrate, by difference' => ['amount' => 0, 'unit' => 'g'],
            'Fiber, total dietary' => ['amount' => 0, 'unit' => 'g'],
            'Sugars, total including NLEA' => ['amount' => 0, 'unit' => 'g'],
            // Inicializa otros nutrientes si los añadiste a getCachedNutrientsForFdcIds
        ];
        $ingredientesConNutrientes = []; // Para pasar a la vista con detalles

        if (!empty($ingredientes)) {
            // Extraer solo los FDC IDs para la consulta
            $fdcIds = array_column($ingredientes, 'fdc_id');

            // 3. Obtener nutrientes cacheados para todos los ingredientes a la vez
            $nutrientesCacheados = $recetaModel->getCachedNutrientsForFdcIds($fdcIds);

            // 4. Iterar sobre cada ingrediente de la receta
            foreach ($ingredientes as $ing) {
                $fdcId = $ing['fdc_id'];
                $cantidadGramos = (float)$ing['cantidad_g'];
                $ing['nutrientes_calculados'] = []; // Array para guardar nutrientes de este ingrediente

                // Verifica si tenemos datos cacheados para este FDC ID
                if (!isset($nutrientesCacheados[$fdcId])) {
                    // Si no están cacheados, llama a la API para obtenerlos (esto también los cachea)
                    $details = $fdcService->getFoodDetails($fdcId);
                    // Vuelve a intentar leer del caché (getFoodDetails ya los guardó)
                    $nutrientesCacheados = $recetaModel->getCachedNutrientsForFdcIds($fdcIds);
                    // Podríamos añadir manejo de error si getFoodDetails falla
                }

                // Ahora sí deberíamos tener los nutrientes cacheados
                if (isset($nutrientesCacheados[$fdcId])) {
                    $nutrientesDelAlimento = $nutrientesCacheados[$fdcId];

                    // 5. Calcular y sumar nutrientes para la cantidad usada
                    foreach ($nutrientesTotales as $nombreNutriente => $data) {
                        if (isset($nutrientesDelAlimento[$nombreNutriente])) {
                            $nutrienteData = $nutrientesDelAlimento[$nombreNutriente];
                            $cantidadPor100g = $nutrienteData['amount'];
                            // Calcula la cantidad para los gramos de la receta
                            $cantidadCalculada = ($cantidadPor100g / 100) * $cantidadGramos;
                            // Suma al total de la receta
                            $nutrientesTotales[$nombreNutriente]['amount'] += $cantidadCalculada;
                            // Guarda el cálculo individual para mostrar en la vista (opcional)
                            $ing['nutrientes_calculados'][$nombreNutriente] = round($cantidadCalculada, 1) . ' ' . $nutrienteData['unit'];
                        }
                    }
                }
                 $ingredientesConNutrientes[] = $ing; // Añade el ingrediente con sus detalles
            }

             // Redondear los totales
             foreach ($nutrientesTotales as $key => $data) {
                 $nutrientesTotales[$key]['amount'] = round($data['amount'], 1);
             }
        }

        view('home/receta_detalle', [
            'title'            => $receta['titulo'],
            'receta'           => $receta,
            'ingredientes'     => $ingredientesConNutrientes, // Ingredientes con sus nutrientes calculados
            'nutrientesTotales'=> $nutrientesTotales      // Totales de la receta
        ]);
    }
    public function create()
    {
        // Proteger la ruta
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        view('home/recetas_crear', ['title' => 'Crear Nueva Receta']);
    }

    // En app/Controllers/RecetasController.php

    // ... (index(), show(), create() existentes) ...

    /**
     * ==========================================
     * NUEVO MÉTODO: Guarda la nueva receta y sus ingredientes
     * ==========================================
     */
    public function store()
    {
        // 1. Proteger ruta
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        $userId = $_SESSION['usuario_id']; // Podríamos guardarlo si recetas tuviera user_id

        $nombreImagenGuardada = null; // Variable para guardar el nombre del archivo

        // Verifica si se subió un archivo y no hubo errores
        if (isset($_FILES['imagen_receta']) && $_FILES['imagen_receta']['error'] === UPLOAD_ERR_OK) {
            
            $file = $_FILES['imagen_receta'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2 MB

            // 1. Validar tipo y tamaño
            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                
                // 2. Generar nombre único
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                // Nombre: userID_timestamp_aleatorio.extension
                $nombreImagenGuardada = $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension; 

                // 3. Definir ruta de destino
                // Asegúrate que esta carpeta exista y tenga permisos de escritura
                $rutaDestino = BASE_PATH . '/public/assets/img/recetas_usuario/' . $nombreImagenGuardada; 
                $directorioDestino = dirname($rutaDestino);

                // Crear directorio si no existe
                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0775, true); 
                }

                // 4. Mover el archivo subido
                if (!move_uploaded_file($file['tmp_name'], $rutaDestino)) {
                    // Error al mover el archivo, resetea el nombre
                    error_log("Error al mover el archivo subido: " . $file['name']);
                    $nombreImagenGuardada = null; 
                    // Podríamos añadir un mensaje de error aquí
                }
                
            } else {
                // Archivo inválido (tipo o tamaño)
                // Podríamos añadir un mensaje de error aquí
                 view('home/recetas_crear', [ /* ... */ 'error' => 'Archivo de imagen inválido (tipo o tamaño).' ]);
                 return;
            }
        }

        // 2. Recoger datos del formulario
        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $categoria = $_POST['categoria'] ?? 'comida de descanso';
        $instrucciones = trim($_POST['instrucciones'] ?? '');
        $ingredientesJson = $_POST['ingredientes_fdc'] ?? '{}'; // Recibe el JSON

        // 3. Validación básica
        if (empty($titulo) || empty($instrucciones) || empty($ingredientesJson)) {
            // Recargar el formulario con un mensaje de error
            view('home/recetas_crear', [
                'title' => 'Crear Nueva Receta',
                'error' => 'Nombre, instrucciones e ingredientes son obligatorios.'
                // Podríamos pasar los datos $_POST de vuelta para rellenar el form
            ]);
            return;
        }

        // 4. Decodificar ingredientes JSON
        $ingredientes = json_decode($ingredientesJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($ingredientes)) {
             view('home/recetas_crear', [ /* ... error ... */ 'error' => 'Error al procesar los ingredientes.' ]);
             return;
        }

        // 5. Guardar en la base de datos (usaremos nuevos métodos en el modelo)
       $recetaModel = new Receta();
        
        // 5. Guardar la receta principal (pasando el nombre de la imagen)
        $nuevaRecetaId = $recetaModel->createRecipe([
            'user_id' => $userId,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'categoria' => $categoria,
            'instrucciones' => $instrucciones,
            'imagen' => $nombreImagenGuardada // <-- Pasa el nombre generado (o null si no hubo imagen)
        ]);
        if (!$nuevaRecetaId) {
             view('home/recetas_crear', [ /* ... error ... */ 'error' => 'Error al guardar la receta.' ]);
             return;
        }

        // 5.2 Guardar los ingredientes asociados
        $exitoIngredientes = $recetaModel->addIngredientsToRecipe($nuevaRecetaId, $ingredientes);

        if (!$exitoIngredientes) {
             // Podríamos borrar la receta creada o mostrar un error más específico
             view('home/recetas_crear', [ /* ... error ... */ 'error' => 'Error al guardar los ingredientes.' ]);
             return;
        }
        $xpPorCrearReceta = 25; // Puntos por crear una receta
        $userModel = new User();
        $resultadoXp = $userModel->addXp($userId, $xpPorCrearReceta);
        if ($resultadoXp['subio_de_nivel']) {
            $_SESSION['flash_message'] = "¡Receta creada! ¡Subiste a: Nivel {$resultadoXp['nombre_nuevo_nivel']}! (+{$xpPorCrearReceta} XP)";
        } else {
            $_SESSION['flash_message'] = "¡Receta creada con éxito! Ganaste +{$xpPorCrearReceta} XP.";
        }
        // 6. Redirigir (ej. a la lista de recetas o al detalle de la nueva)
        header('Location: ' . url('/mis-recetas')); // Redirige a la lista
        exit;
    }
    // En app/Controllers/RecetasController.php

    // ... (create() y store() existentes) ...

    /**
     * ==========================================
     * NUEVO MÉTODO: Muestra las recetas creadas por el usuario
     * ==========================================
     */
    public function myRecipes()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login')); exit;
        }
        $userId = $_SESSION['usuario_id'];

        $model = new Receta();
        $misRecetas = $model->findByUserId($userId);

        view('home/mis_recetas', [
            'title' => 'Mis Recetas',
            'recetas' => $misRecetas // Pasamos las recetas del usuario a la vista
        ]);
    }
}
<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\AreaMejora; // Modelo para la nueva tabla
use App\Models\PlanUsuario;
use App\Models\Receta;
use App\Models\Trivia; // Nuevo modelo para preguntas de trivia
use App\Models\Ejercicio;

class HabitosController extends Controller
{
    /**
     * Muestra la página principal de hábitos
     */
    public function index(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login')); exit;
        }
        $userId = $_SESSION['usuario_id'];

        // --- NUEVA LÓGICA ---
        $areaModel = new AreaMejora();
        $areasDisponibles = $areaModel->getAll();
        view('home/habitos', [
            'title' => 'Elige tu Hábito',
            'areas' => $areasDisponibles // Pasa las áreas a la vista
            // 'planActivo' => $planActivo (después)
        ]);
        // -----------------
    }

    /**
     * NUEVO MÉTODO: Muestra el formulario para registrar hábitos
     * Esto responderá a la ruta /habitos-form
     */
    public function create(): void
    {
        $title = 'Configura tus Hábitos';

        // Asumimos que la vista se llamará 'habitos_form.php'
        // y está en 'app/Views/home/'
        $this->render('home/habitos_form', compact('title'));
    }

    // ... en app/Controllers/HabitosController.php
    public function store(): void
    {
        // 1. Asegurarnos de que el usuario esté logueado
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $userId = $_SESSION['usuario_id'];

        // 2. Recolectar los datos
        $data = [
            'nivel_actividad' => $_POST['nivel_actividad'] ?? 'sedentario',
            'objetivo_principal' => $_POST['objetivo_principal'] ?? '',
            'nivel_alimentacion' => $_POST['nivel_alimentacion'] ?? 'novato',
            'horas_sueno' => (int) ($_POST['horas_sueno'] ?? 0),
            'consumo_agua' => (int) ($_POST['consumo_agua'] ?? 0),
            // === NUEVOS CAMPOS ===
            'peso' => (float) ($_POST['peso'] ?? 0),
            'altura' => (float) ($_POST['altura'] ?? 0),
            'genero' => $_POST['genero'] ?? null,
        ];

        // 3. Calcular el IMC
        $imc = 0;
        if ($data['altura'] > 0 && $data['peso'] > 0) {
            $altura_m = $data['altura'] / 100; // Convertir cm a metros
            // IMC = peso / (altura * altura)
            $imc = round($data['peso'] / ($altura_m * $altura_m), 1);
        }
        $data['imc'] = $imc;

        // 4. Actualizar el usuario en la BD
        $userModel = new User();
        $userModel->updateProfile($userId, $data);

        // 5. Redirigir al dashboard principal
        header('Location: ' . url('/'));
        exit;
    }

   public function showPlan(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login')); exit;
        }
        $userId = $_SESSION['usuario_id'];
        $areaCodigo = $_GET['area'] ?? null;

        if (!$areaCodigo) {
            header('Location: ' . url('/habitos')); exit;
        }

        $areaModel = new AreaMejora();
        $area = $areaModel->findByCode($areaCodigo);
        if (!$area) {
            header('Location: ' . url('/habitos')); exit;
        }
        $trivia = [];
        // --- Lógica de Módulos ---
        // Usamos un switch para cargar datos y vistas diferentes
        switch ($areaCodigo) {
            case 'desayuno':
                $recetaModel = new Receta();
                // Buscamos 3 recetas marcadas como 'es_rapido'
                $recetasRapidas = $recetaModel->findByTag('es_rapido', 3);
                
                view('home/plan_desayuno', [ // <-- Nueva vista
                    'title'   => $area['titulo'],
                    'area'    => $area,
                    'recetas' => $recetasRapidas
                ]);
                break;
            case 'frutas_verduras':
                // === 2. ESTA ES LA LÓGICA CORREGIDA ===
                $triviaModel = new Trivia();
                // === CAMBIO: Pasa un array de categorías ===
                $triviaData = $triviaModel->getRandomQuestion(['Nutrición', 'Frutas y Verduras']);
                
                if ($triviaData) {
                    $trivia = [
                       'pregunta' => $triviaData['pregunta'],
                       'opciones' => array_filter([ // Filtra opciones nulas
                           'a' => $triviaData['opcion_a'] ?? null,
                           'b' => $triviaData['opcion_b'] ?? null,
                           'c' => $triviaData['opcion_c'] ?? null,
                           'd' => $triviaData['opcion_d'] ?? null,
                       ]),
                       'correcta' => $triviaData['respuesta_correcta'],
                       'feedback_correcta' => $triviaData['feedback_correcto'],
                       'feedback_incorrecta' => $triviaData['feedback_incorrecto']
                    ];
                } else {
                    // Fallback si no hay preguntas en la BD
                    $trivia = ['pregunta' => 'Trivia no disponible.', 'opciones' => []];
                }
                
                // Vista específica para frutas y verduras
                view('home/plan_frutas_verduras', [ 
                    'title'   => $area['titulo'],
                    'area'    => $area,
                    'recetas' => [], // Pasamos array vacío como dijimos
                    'trivia'  => $trivia
                ]);
                return; //
                // ------------------------------------
            case 'dormir':
                
                // (No necesitamos cargar recetas ni trivia aquí por ahora)
                
                view('home/plan_dormir', [ // <-- Nueva vista
                    'title'   => $area['titulo'],
                    'area'    => $area
                ]);
                break;
            // Aquí irían los 'case' para otros módulos (dormir, moverse, etc.)
            // case 'dormir':
            //     view('home/plan_dormir', [...]);
            //     break;
            case 'moverse':
                $ejercicioModel = new Ejercicio();
                
                // Buscamos 3 ejercicios aleatorios que sean "Sin Equipo" y "Calentamiento" o "Fuerza"
                $ejerciciosSugeridos = $ejercicioModel->findByFilter(
                    ['equipamiento' => 'Sin Equipo', 'tipo_entrenamiento' => 'Fuerza'], 
                    3 // Traer 3 ejemplos
                );
                // Si no encuentra, busca calentamiento
                if (empty($ejerciciosSugeridos)) {
                     $ejerciciosSugeridos = $ejercicioModel->findByFilter(
                        ['equipamiento' => 'Sin Equipo', 'tipo_entrenamiento' => 'Calentamiento'], 3
                    );
                }

                // Datos para el Mini-Test (Módulo 4)
                $miniTest = [
                   'pregunta' => '¿Qué tipo de movimiento disfrutas más?',
                   'opciones' => [
                       'bailar' => ['nombre' => 'Bailar 💃', 'tipo' => 'Cardio'],
                       'caminar' => ['nombre' => 'Caminar / Correr 🏃‍♀️', 'tipo' => 'Cardio'],
                       'fuerza' => ['nombre' => 'Fuerza Ligera (en casa) 🏋️‍♂️', 'tipo' => 'Fuerza']
                   ]
                ];
                
                view('home/plan_moverse', [ // <-- Nueva vista
                    'title'   => $area['titulo'],
                    'area'    => $area,
                    'ejercicios' => $ejerciciosSugeridos, // Ejercicios "Sin Equipo"
                    'miniTest'  => $miniTest // Datos para el test interactivo
                ]);
                break;
            default:
                // Vista por defecto si el módulo no está implementado
                echo "Módulo '{$areaCodigo}' en construcción.";
                break;
        }
        // Ya no usamos la tabla planes_usuarios por ahora
    }
}
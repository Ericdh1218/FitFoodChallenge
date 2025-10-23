<?php
namespace App\Services;

class NutricionService
{
    /**
     * Calcula una estimación de calorías y macros diarios.
     * Utiliza la fórmula Mifflin-St Jeor.
     * @param array $usuario Datos del usuario.
     * @return array|null Un array con ['calorias', ...] o null.
     */
    public static function calcularEstimacionNutricional(array $usuario): ?array
    {
        // Validar datos necesarios
        if (empty($usuario['peso']) || empty($usuario['altura']) || empty($usuario['Edad']) || empty($usuario['genero']) || empty($usuario['nivel_actividad'])) {
            return null; // Faltan datos
        }

        $peso_kg = (float)$usuario['peso'];
        $altura_cm = (float)$usuario['altura'];
        $edad_anos = (int)$usuario['Edad']; // Asegúrate que 'Edad' exista en $usuario
        $genero = $usuario['genero']; 
        $nivel_actividad = $usuario['nivel_actividad']; 

        // 1. Calcular GEB (Mifflin-St Jeor)
        $geb = 0;
        if ($genero === 'masculino') {
            $geb = (10 * $peso_kg) + (6.25 * $altura_cm) - (5 * $edad_anos) + 5;
        } elseif ($genero === 'femenino') {
            $geb = (10 * $peso_kg) + (6.25 * $altura_cm) - (5 * $edad_anos) - 161;
        } else {
            return null; 
        }

        // 2. Multiplicar por FAF
        $faf = 1.2; 
        switch ($nivel_actividad) {
            case 'ligero':     $faf = 1.375; break;
            case 'activo':     $faf = 1.55;  break;
            case 'muy_activo': $faf = 1.725; break;
        }
        $calorias_diarias = round($geb * $faf);

        // 3. Calcular Macros (Ejemplo 40/30/30)
        $carbos_gramos = round(($calorias_diarias * 0.40) / 4); 
        $proteinas_gramos = round(($calorias_diarias * 0.30) / 4); 
        $grasas_gramos = round(($calorias_diarias * 0.30) / 9);   

        return [
            'calorias'  => $calorias_diarias,
            'proteinas' => $proteinas_gramos,
            'grasas'    => $grasas_gramos,
            'carbos'    => $carbos_gramos,
        ];
    }
}
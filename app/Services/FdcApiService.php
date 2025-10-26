<?php
namespace App\Services;

use App\Config\DB; // Asumiendo que tu conexión DB está aquí
use PDO;

class FdcApiService
{
    private $apiKey;
    private $pdo;
    private $apiBaseUrl = 'https://api.nal.usda.gov/fdc/v1';

    
    /**
     * Busca alimentos en la API de FDC.
     * Devuelve resultados básicos o null si hay error.
     */public function __construct()
    {
        // === CAMBIO: Intenta leer con getenv() ===
        $apiKeyFromEnv = $_ENV['FDC_API_KEY'] ?? null; // Sigue intentando leer $_ENV
        $apiKeyFromGetenv = getenv('FDC_API_KEY'); // Intenta con getenv()

        // === AÑADE ESTAS LÍNEAS DE LOGGING ===
        error_log("FdcApiService Constructor: Valor desde \$_ENV = " . ($apiKeyFromEnv ? 'Encontrado' : 'NO Encontrado'));
        error_log("FdcApiService Constructor: Valor desde getenv() = " . ($apiKeyFromGetenv ? 'Encontrado (' . $apiKeyFromGetenv . ')' : 'NO Encontrado'));
        // =====================================

        // Usa getenv si está disponible, si no, usa $_ENV
        $this->apiKey = $apiKeyFromGetenv ?: $apiKeyFromEnv; 
        
        if (!$this->apiKey) {
            error_log("FDC API Key NO ESTÁ CONFIGURADA después de intentar $_ENV y getenv()"); // Mensaje más específico
        }
        $this->pdo = DB::conn();
    }
    public function searchFoods(string $query, int $pageSize = 10): ?array
    {
        if (!$this->apiKey) return null;

        $endpoint = $this->apiBaseUrl . "/foods/search?api_key={$this->apiKey}";
        $payload = json_encode([
            'query' => $query,
            'dataType' => ["Foundation", "SR Legacy", "Branded"], // Tipos de datos a buscar
            'pageSize' => $pageSize,
            'pageNumber' => 1
        ]);

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => $payload,
                'ignore_errors' => true // Para poder leer la respuesta aunque sea un error 4xx/5xx
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($endpoint, false, $context); // Usamos @ para suprimir warnings si falla la conexión

        if ($response === FALSE) {
            error_log("Error al conectar con FDC API (Search)");
            return null; // Error de conexión
        }

        $data = json_decode($response, true);

        // Verificar si hubo un error en la respuesta de la API (ej. clave inválida)
        $http_code = $this->parseHttpCode($http_response_header);
        if ($http_code >= 400 || !isset($data['foods'])) {
             error_log("Error FDC API (Search): Code {$http_code} - " . ($data['message'] ?? $response));
             return null;
        }

        // Cachear los resultados básicos en nuestra BD
        $this->cacheBasicFoodData($data['foods']);

        return $data['foods']; // Devuelve la lista de alimentos encontrados
    }

    /**
     * Guarda la información BÁSICA de los alimentos en la tabla local.
     */
    private function cacheBasicFoodData(array $foods)
    {
        if (empty($foods)) return;

        // Prepara la consulta para insertar o actualizar (si ya existe)
        $sql = "INSERT INTO alimentos_fdc (fdc_id, descripcion, data_type, brand_owner, brand_name)
                VALUES (:fdc_id, :desc, :type, :owner, :name)
                ON DUPLICATE KEY UPDATE
                    descripcion = VALUES(descripcion),
                    data_type = VALUES(data_type),
                    brand_owner = VALUES(brand_owner),
                    brand_name = VALUES(brand_name),
                    updated_at = CURRENT_TIMESTAMP";
        $st = $this->pdo->prepare($sql);

        foreach ($foods as $food) {
            $st->execute([
                ':fdc_id' => $food['fdcId'],
                ':desc'   => $food['description'],
                ':type'   => $food['dataType'] ?? 'N/A',
                ':owner'  => $food['brandOwner'] ?? null,
                ':name'   => $food['brandName'] ?? null
                // Nota: category no suele venir en la búsqueda básica
            ]);
        }
    }
    
    // Función helper simple para obtener código HTTP de los headers
    private function parseHttpCode(array $headers): int {
        if(isset($headers[0]) && strpos($headers[0], 'HTTP/') === 0) {
            $parts = explode(' ', $headers[0]);
            if(isset($parts[1])) return (int)$parts[1];
        }
        return 0;
    }

    // ... (después de parseHttpCode() o cacheBasicFoodData()) ...

    /**
     * Obtiene los detalles completos de un alimento desde la API de FDC.
     * Devuelve los datos decodificados o null si hay error.
     */
    public function getFoodDetails(int $fdcId): ?array
    {
        if (!$this->apiKey) {
            error_log("getFoodDetails: API Key no configurada.");
            return null;
        }

        // Endpoint para detalles de un alimento específico
        $endpoint = $this->apiBaseUrl . "/food/{$fdcId}?api_key={$this->apiKey}";
        // Formato opcional (puedes ajustar qué nutrientes pedir si quieres optimizar)
        // $endpoint .= "&format=abridged&nutrients=203&nutrients=204&nutrients=205&nutrients=208"; // Ejemplo: Prot, Grasa, Carbs, Energía

        $options = [
            'http' => [
                'method' => 'GET', // Petición GET para detalles
                'ignore_errors' => true
            ]
        ];
        $context = stream_context_create($options);
        $response = @file_get_contents($endpoint, false, $context);

        if ($response === FALSE) {
            error_log("Error al conectar con FDC API (Details for FDC ID: {$fdcId})");
            return null; // Error de conexión
        }

        $data = json_decode($response, true);

        // Verificar si hubo un error en la respuesta de la API
        $http_code = $this->parseHttpCode($http_response_header ?? []); // Pasa los headers
        if ($http_code >= 400 || !$data) {
             error_log("Error FDC API (Details for FDC ID: {$fdcId}): Code {$http_code} - " . ($data['message'] ?? $response));
             return null;
        }

        // Cachear los detalles obtenidos (nutrientes y porciones)
        $this->cacheNutrientData($fdcId, $data);
        $this->cachePortionData($fdcId, $data);

        return $data;
    }
    /**
     * Guarda (cachea) los nutrientes detallados de un alimento en la BD local.
     */
    private function cacheNutrientData(int $fdcId, array $foodDetails)
    {
        // La info nutricional suele estar en $foodDetails['foodNutrients']
        if (!isset($foodDetails['foodNutrients']) || !is_array($foodDetails['foodNutrients'])) {
            return; // No hay datos de nutrientes
        }

        // Preparamos la consulta (INSERT IGNORE para evitar duplicados si ya cacheamos antes)
        $sql = "INSERT IGNORE INTO alimento_nutrientes
                    (fdc_id, nutrient_id, nutrient_name, unit_name, amount_per100)
                VALUES
                    (:fdc_id, :n_id, :n_name, :u_name, :amount)";
        $stNutrient = $this->pdo->prepare($sql);

        foreach ($foodDetails['foodNutrients'] as $nutrient) {
            // Asegurarnos que tenemos los datos mínimos
            if (!isset($nutrient['nutrient']['id'], $nutrient['nutrient']['name'], $nutrient['nutrient']['unitName'], $nutrient['amount'])) {
                continue; // Saltar si falta algún dato clave
            }
            
            // La API a veces devuelve amount por porción, debemos normalizar a 100g
            // Por ahora, asumiremos que los datos principales vienen por 100g (ajustar si es necesario)
            // Esto es complejo, ya que depende del dataType (Foundation, SR Legacy, Branded)
            // Para simplificar, guardaremos el 'amount' tal cual viene si está presente
            $amountPer100 = $nutrient['amount'] ?? 0; // Tomar amount si existe, si no 0

            $stNutrient->execute([
                ':fdc_id' => $fdcId,
                ':n_id'   => $nutrient['nutrient']['id'],
                ':n_name' => $nutrient['nutrient']['name'],
                ':u_name' => $nutrient['nutrient']['unitName'],
                ':amount' => $amountPer100
            ]);
        }
    }

    /**
     * Guarda (cachea) las porciones de un alimento en la BD local.
     */
    private function cachePortionData(int $fdcId, array $foodDetails)
    {
        // Las porciones suelen estar en $foodDetails['foodPortions']
        if (!isset($foodDetails['foodPortions']) || !is_array($foodDetails['foodPortions'])) {
            return; // No hay datos de porciones
        }

        // Preparamos la consulta (INSERT IGNORE)
        $sql = "INSERT IGNORE INTO alimento_porciones
                    (fdc_id, portion_desc, amount, gram_weight, measure_unit)
                VALUES
                    (:fdc_id, :desc, :amount, :grams, :unit)";
        $stPortion = $this->pdo->prepare($sql);

        foreach ($foodDetails['foodPortions'] as $portion) {
            // Datos comunes: portionDescription, gramWeight
            // Datos a veces presentes: amount, measureUnit
            if (!isset($portion['gramWeight'])) {
                 continue; // El peso en gramos es esencial
            }
            
            // Descripción: a veces usa 'portionDescription', a veces 'modifier'
            $description = $portion['portionDescription'] ?? $portion['modifier'] ?? 'N/A';
            
            $stPortion->execute([
                ':fdc_id' => $fdcId,
                ':desc'   => $description,
                ':amount' => $portion['amount'] ?? null, // Puede ser nulo
                ':grams'  => $portion['gramWeight'],
                ':unit'   => $portion['measureUnit']['name'] ?? null // Puede ser nulo
            ]);
        }
    }
    // --- AQUÍ AÑADIREMOS getFoodDetails($fdcId) y cacheDetailedFoodData() DESPUÉS ---
}
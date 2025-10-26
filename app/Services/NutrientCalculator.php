<?php
namespace App\Services;
use PDO;

class NutrientCalculator {
    public function __construct(private PDO $db) {}

    // IDs FDC comunes
    private array $ids = [
        'kcal'=>1008, 'protein'=>1003, 'carbs'=>1005, 'fat'=>1004, 'fiber'=>1079,
        'sodium'=>1093, 'potassium'=>1092, 'calcium'=>1087, 'iron'=>1089,
        'vit_c'=>1162, 'vit_a_rae'=>1106,
    ];

    public function totalsForRecipe(int $recetaId): array {
        $sql = "SELECT ri.cantidad_g, an.nutrient_id, an.amount_per100
                FROM receta_ingredientes ri
                JOIN alimento_nutrientes an ON an.fdc_id = ri.fdc_id
                WHERE ri.receta_id = ?";
        $st = $this->db->prepare($sql);
        $st->execute([$recetaId]);

        $tot = [];
        while ($r = $st->fetch(\PDO::FETCH_ASSOC)) {
            $aporte = (float)$r['amount_per100'] * ((float)$r['cantidad_g'] / 100.0);
            $nid = (int)$r['nutrient_id'];
            $tot[$nid] = ($tot[$nid] ?? 0) + $aporte;
        }

        $out = [];
        foreach ($this->ids as $key => $nid) $out[$key] = round($tot[$nid] ?? 0, 2);
        return $out; // totales de la receta completa
    }
}

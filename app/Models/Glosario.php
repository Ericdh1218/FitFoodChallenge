<?php
namespace App\Models;

use App\Config\DB;
use PDO;

class Glosario
{
    protected $pdo;

    public function __construct() { $this->pdo = DB::conn(); }

    /** Obtiene todos los términos ordenados */
    public function all(): array
    {
        $sql = "SELECT id, termino, definicion, categoria FROM glosario ORDER BY termino ASC";
        $st = $this->pdo->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
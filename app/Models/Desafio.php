<?php
namespace App\Models;
use App\Config\DB;
use PDO;

class Desafio
{
    protected $pdo;
    public function __construct() { $this->pdo = DB::conn(); }

    public function getAll(): array
    {
        return $this->pdo->query("SELECT * FROM desafios")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByCodigo(string $codigo): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM desafios WHERE codigo = :codigo");
        $st->execute([':codigo' => $codigo]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
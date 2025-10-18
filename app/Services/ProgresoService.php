<?php
namespace App\Services;

use App\Repositories\ProgresoRepository;

class ProgresoService
{
    public function __construct(private ?ProgresoRepository $repo = null)
    {
        $this->repo ??= new ProgresoRepository();
    }

    public function saveToday(int $userId, int $minutes, int $water): bool
    {
        if ($minutes > 600 || $water > 30) return false; // límites sanos
        return $this->repo->upsertDaily($userId, $minutes, $water);
    }
}

<?php
namespace App\Controllers;

use App\Models\User;
use App\Services\BadgeService;

class RankingController
{
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login')); exit;
        }
        $userId = (int)$_SESSION['usuario_id'];

        $userModel  = new User();
        $usuarios   = $userModel->getLeaderboard(10);     // ya filtra tipo_user=0
        $miRanking  = $userModel->getUserRank($userId);    // ya calcula entre no-admins

        // Insignias
        $badgeSvc = new BadgeService();

        $insignias  = $badgeSvc->getAll();                 // todas
        $mias       = $badgeSvc->getUserBadgeCodes($userId); // codigos conseguidos

        view('home/ranking', [
            'title'      => 'Ranking',
            'usuarios'   => $usuarios,
            'miRanking'  => $miRanking,
            'insignias'  => $insignias,
            'misBadges'  => $mias,
        ]);
    }
}

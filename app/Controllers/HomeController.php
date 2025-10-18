<?php
namespace App\Controllers;
class HomeController {
    public function index(): void {
        $title = 'Inicio';
        view('home/index', compact('title'));
    }
}

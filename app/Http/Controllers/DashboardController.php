<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $simulations = $user->simulations;

        return view('dashboard', [
            'title' => 'Tableau de bord',
            'header' => 'Bienvenue '.auth()->user()->first_name.' !',
            'breadcrumbs' => [
                [
                    'label' => 'Accueil',
                ],
            ],
        ]);
    }
}

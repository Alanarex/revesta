<?php

namespace App\Http\Controllers;

use App\View\ViewModels\DashboardViewModel;

class DashboardController extends Controller
{
    public function index()
    {
        $user=auth()->user();
        $simulations=$user->simulations;
        
        return view('dashboard', [
            'title' => 'Tableau de bord',
            'header' => 'Bienvenue ' . auth()->user()->first_name . ' !',
            'breadcrumbs' => [
                [
                    'label' => 'Accueil',
                    'url' => route('dashboard'),
                ],
            ],
        ]);
    }
}

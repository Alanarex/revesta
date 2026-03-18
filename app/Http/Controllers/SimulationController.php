<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexSimulationRequest;
use App\Http\Requests\ShowSimulationRequest;
use App\Models\Simulation;
use App\Services\SimulationService;
use Illuminate\View\View;

class SimulationController extends Controller
{
    public function __construct(protected SimulationService $simulationService) {}

    public function index(IndexSimulationRequest $request): View
    {
        $user = auth()->user();
        $isAdmin = $user !== null && method_exists($user, 'isAdmin') && $user->isAdmin();

        $validated = $request->validated();
        $firstName = $isAdmin ? ($validated['first_name'] ?? null) : null;
        $lastName = $isAdmin ? ($validated['last_name'] ?? null) : null;
        $perPage = (int) ($validated['per_page'] ?? 12);

        $simulations = $this->simulationService->getManagementSimulations(
            viewer: $user,
            isAdmin: $isAdmin,
            perPage: $perPage,
            firstName: $firstName,
            lastName: $lastName,
        );

        return view('simulations.index', [
            'title' => 'Gestion des simulations',
            'simulations' => $simulations,
            'isAdmin' => $isAdmin,
            'filters' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
            ],
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Simulations'],
            ],
        ]);
    }

    public function show(ShowSimulationRequest $request, Simulation $simulation): View
    {
        $user = auth()->user();
        $isAdmin = $user !== null && method_exists($user, 'isAdmin') && $user->isAdmin();

        $loadedSimulation = $this->simulationService->findVisibleSimulation($user, $isAdmin, $simulation->id);
        abort_if($loadedSimulation === null, 404);

        return view('simulations.show', [
            'title' => 'Détail de la simulation #'.$loadedSimulation->id,
            'simulation' => $loadedSimulation,
            'isAdmin' => $isAdmin,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Simulations', 'url' => route('simulations.index')],
                ['label' => '#'.$loadedSimulation->id],
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSimulationRequest;
use App\Services\SimulationService;
use Illuminate\Http\JsonResponse;

class SimulationController extends Controller
{
    public function __construct(protected SimulationService $simulationService) {}

    /**
        * Submit a simulation payload from the REVESTA extension.
        *
        * This public endpoint stores the ad snapshot, upserts the user profile, creates the
        * simulation record, syncs renovation works/aids, and queues the simulation report email.
        *
        * @group Simulations
        *
        * @bodyParam annonce object required Ad payload extracted by the extension.
        * @bodyParam annonce.url string required Listing URL (http/https). Example: https://www.seloger.com/annonces/achat/appartement/paris-15eme-75/123456789.htm
        * @bodyParam annonce.site string nullable Source website. Example: seloger
        * @bodyParam annonce.titre string nullable Listing title. Example: Appartement 3 pièces avec balcon
        * @bodyParam annonce.prix number nullable Listing price. Example: 350000
        * @bodyParam annonce.ville string nullable City name. Example: Paris
        * @bodyParam annonce.code_postal string nullable Postal code. Example: 75015
        * @bodyParam annonce.surface number nullable Area in m². Example: 62
        * @bodyParam annonce.pieces number nullable Number of rooms. Example: 3
        * @bodyParam annonce.type_logement string nullable Housing type label. Example: Appartement
        * @bodyParam annonce.dpe string nullable DPE class code. Example: E
        * @bodyParam annonce.images array nullable Listing images.
        * @bodyParam annonce.images.* string nullable Image URL (http/https). Example: https://images.example.com/ad-123/main.jpg
        *
        * @bodyParam utilisateur object required User payload.
        * @bodyParam utilisateur.email string required User email. Example: jean.dupont@example.com
        * @bodyParam utilisateur.prenom string nullable First name. Example: Jean
        * @bodyParam utilisateur.nom string nullable Last name. Example: Dupont
        * @bodyParam utilisateur.telephone string nullable Phone number. Example: 0601020304
        * @bodyParam utilisateur.code_postal string nullable Postal code. Example: 75015
        * @bodyParam utilisateur.revenus number nullable Household income. Example: 42000
        * @bodyParam utilisateur.nombre_personnes integer nullable Household size. Example: 2
        * @bodyParam utilisateur.statut string nullable Occupancy status label. Example: Propriétaire occupant
        * @bodyParam utilisateur.dpe_actuel string nullable Current DPE class. Example: F
        * @bodyParam utilisateur.dpe_vise string nullable Target DPE class. Example: D
        * @bodyParam utilisateur.periode_construction string nullable Construction period label. Example: 1949 - 1974
        * @bodyParam utilisateur.type_logement string nullable Housing type label. Example: Appartement
        * @bodyParam utilisateur.gain_energetique integer nullable Targeted class gain. Example: 2
        * @bodyParam utilisateur.parcours_aide string nullable Aid path label. Example: MaPrimeRénov'
        *
        * @bodyParam simulation object required Simulation payload.
        * @bodyParam simulation.gain_energetique number nullable Estimated gain. Example: 2
        * @bodyParam simulation.parcours_aide string nullable Aid path label. Example: MaPrimeRénov'
        * @bodyParam simulation.condition_depenses boolean nullable Spending conditions met. Example: true
        * @bodyParam simulation.montant_total_aides number nullable Estimated total aid amount. Example: 18500
        * @bodyParam simulation.pourcentage_bien number nullable Aid coverage percentage. Example: 5.28
        * @bodyParam simulation.travaux array nullable Renovation work labels.
        * @bodyParam simulation.travaux.* string nullable Renovation work label. Example: Isolation des murs
        * @bodyParam simulation.aides_details array nullable Detailed aid list.
        * @bodyParam simulation.aides_details.*.nom string nullable Aid name. Example: MaPrimeRénov'
        * @bodyParam simulation.aides_details.*.montant number nullable Aid amount. Example: 12000
        * @bodyParam simulation.aides_details.*.type string nullable Aid type. Example: subvention
        * @bodyParam simulation.aides_details.*.description string nullable Aid description. Example: Prime pour rénovation énergétique
        * @bodyParam simulation.aides_details.*.url string nullable Aid information URL. Example: https://www.maprimerenov.gouv.fr/
        *
        * @response status=201 {
        *   "success": true,
        *   "message": "Simulation enregistrée avec succès.",
        *   "simulation_id": 154,
        *   "user_id": 87,
        *   "user_created": true
        * }
        * @response status=422 {
        *   "message": "The annonce.url field is required.",
        *   "errors": {
        *     "annonce.url": [
        *       "The annonce.url field is required."
        *     ]
        *   }
        * }
        * @response status=429 {
        *   "message": "Too Many Attempts."
        * }
     */
    public function store(StoreSimulationRequest $request): JsonResponse
    {
        $result = $this->simulationService->submit(
            $request->validated(),
            $request->originalPayload(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Simulation enregistrée avec succès.',
            'simulation_id' => $result['simulation']->id,
            'user_id' => $result['user']->id,
            'user_created' => $result['user_created'],
        ], 201);
    }
}

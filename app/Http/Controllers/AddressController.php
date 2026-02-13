<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAddressRequest;
use App\Http\Requests\DeleteAddressRequest;
use App\Http\Requests\EditAddressRequest;
use App\Http\Requests\IndexAddressRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(
        protected AddressService $addressService
    ) {
    }

    /**
     * Display a datatable of all addresses.
     */
    public function index(IndexAddressRequest $request): View
    {
        $totalCount = $this->addressService->getAddressCount();

        return view('admin.addresses.index', [
            'title' => 'Gestion des addresses',
            'totalCount' => $totalCount,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Adresses', 'url' => route('admin.addresses.index')],
            ],
        ]);
    }

    /**
     * Get all addresses as JSON for DataTable.
     */
    public function list(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'label');
        $direction = $request->get('direction', 'asc');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 50);

        $addresses = $this->addressService->getAllAddresses($perPage, $search, $sort, $direction);

        $data = collect($addresses->items())->map(function ($address) {
            $actions = [
                [
                    'type' => 'edit',
                    'label' => 'Modifier',
                    'icon' => 'fa-edit',
                    'route' => route('admin.addresses.edit', $address),
                    'class' => '',
                ],
                [
                    'type' => 'delete',
                    'label' => 'Supprimer',
                    'icon' => 'fa-trash',
                    'route' => route('admin.addresses.destroy', $address),
                    'needs_confirm' => true,
                    'confirm_message' => "Êtes-vous sûr de vouloir supprimer l'adresse {$address->label} ?",
                    'class' => 'text-danger',
                ],
            ];

            return [
                'id' => $address->id,
                'label' => $address->label,
                'street' => ($address->number ?? '-') . ' ' . $address->street,
                'postal_code' => $address->postal_code,
                'city' => $address->city,
                'departement' => $address->departement ?? '-',
                'actions' => $actions,
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $addresses->total(),
            'per_page' => $perPage,
            'current_page' => $page,
        ]);
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(CreateAddressRequest $request): View
    {
        return view('admin.addresses.create', [
            'title' => 'Créer une nouvelle adresse',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Adresses', 'url' => route('admin.addresses.index')],
                ['label' => 'Créer', 'url' => route('admin.addresses.create')],
            ],
        ]);
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        try {
            $address = $this->addressService->createAddress($request->validated());

            return redirect()->route('admin.addresses.index')
                ->with('success', 'Adresse créée avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'adresse: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified address.
     */
    public function edit(EditAddressRequest $request, Address $address): View
    {
        // Load related users and housings
        $address->load(['users', 'housings']);
        
        return view('admin.addresses.edit', [
            'address' => $address,
            'title' => 'Modifier l\'adresse',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Adresses', 'url' => route('admin.addresses.index')],
                ['label' => $address->label, 'url' => route('admin.addresses.edit', $address)],
                ['label' => 'Modifier', 'url' => route('admin.addresses.edit', $address)],
            ],
        ]);
    }

    /**
     * Update the specified address in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address)
    {
        try {
            $updatedAddress = $this->addressService->updateAddress($address, $request->validated());

            return redirect()->route('admin.addresses.index')
                ->with('success', 'Adresse mise à jour avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'adresse: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified address from storage.
     */
    public function destroy(DeleteAddressRequest $request, Address $address)
    {
        try {
            $this->addressService->deleteAddress($address);

            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json(['success' => true, 'message' => 'Adresse supprimée avec succès!']);
            }

            return redirect()->route('admin.addresses.index')
                ->with('success', 'Adresse supprimée avec succès!');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression de l\'adresse: ' . $e->getMessage()], 500);
            }

            return redirect()->route('admin.addresses.index')
                ->with('error', 'Erreur lors de la suppression de l\'adresse: ' . $e->getMessage());
        }
    }
}

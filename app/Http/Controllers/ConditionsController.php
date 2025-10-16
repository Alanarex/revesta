<?php

namespace App\Http\Controllers;

use App\Http\Requests\Conditions\UpdateConditionsRequest;
use App\Models\Address;
use App\Models\Condition;
use App\Models\FiscalIncomeRange;
use App\Models\Housing;
use App\Models\User;
use Illuminate\Http\Request;

class ConditionsController extends Controller
{
    /**
     * Display the conditions page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $conditionsGrouped = Condition::with('aid') // assumes Condition belongsTo Aid
            ->orderBy('aid_id')
            ->get()
            ->groupBy('aid_id');
        // dd($conditionsGrouped);
            return view('conditions.index', [
            'conditionsGrouped' => $conditionsGrouped,
            'models' => [
                'user' => [
                    'label' => 'Utilisateur',
                    'attributes' => User::getFilterableAttributes(),
                    'types' => User::getFilterableAttributeTypes()
                ],
                'housing' => [
                    'label' => 'Logement',
                    'attributes' => Housing::getFilterableAttributes(),
                    'types' => Housing::getFilterableAttributeTypes()
                ],
                'address' => [
                    'label' => 'Adresse',
                    'attributes' => Address::getFilterableAttributes(),
                    'types' => Address::getFilterableAttributeTypes()
                ],
                'fiscal_income' => [
                    'label' => 'Revenu fiscal',
                    'attributes' => FiscalIncomeRange::getFilterableAttributes(),
                    'types' => FiscalIncomeRange::getFilterableAttributeTypes(),
                    'options' => FiscalIncomeRange::optionsForSelect()
                ]
            ],
            'operators' => ['=', '!=', '<', '<=', '>', '>='],
            'types' => ['Valeur', 'Intervalle'],
            'title' => 'Conditions des aides',
            'header' => 'Conditions',
            'breadcrumbs' => [
                [
                    'label' => 'Accueil',
                    'url' => route('dashboard'),
                ],
                [
                    'label' => 'Conditions',
                    'url' => route('conditions.index'),
                ],
            ],
        ]);
        
    }

    /**
     * Update the conditions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateConditionsRequest $request)
    {
        dd($request->all());
        foreach ($request->validated()['conditions'] as $id => $data) {
            Condition::where('id', $id)->update($data);
        }

        return redirect()->route('conditions.index')->with('success', 'Conditions mises à jour.');
    }

}

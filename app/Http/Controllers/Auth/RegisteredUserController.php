<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request, RegistrationService $registration): RedirectResponse
    {
        try {
            $registration->registerAndLogin($request->validated());
            return redirect(route('login'))->with('success', 'Inscription reussie! Veuillez verifier votre email et definir votre mot de passe pour vous connecter.');
        } catch (\Exception $e) {
            return redirect(route('register'))->withInput()->with('error', 'Erreur lors de l\'inscription: ' . $e->getMessage());
        }
    }
}

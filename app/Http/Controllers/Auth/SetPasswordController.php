<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SetPasswordRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SetPasswordController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    /**
     * Show the set password form.
     */
    public function show(User $user): View
    {
        // Verify the user has a valid signed request
        return view('auth.set-password', [
            'user' => $user,
        ]);
    }

    /**
     * Store the password and verify email.
     */
    public function store(SetPasswordRequest $request, User $user): RedirectResponse
    {
        try {
            // Set password
            $this->authService->updatePassword($user, $request->password);

            // Mark email as verified
            $user->markEmailAsVerified();
            event(new Verified($user));

            // Redirect to login with success message
            return redirect(route('login'))->with('success', 'Votre mot de passe a ete defini avec succes! Veuillez vous connecter.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la definition du mot de passe: '.$e->getMessage());
        }
    }
}

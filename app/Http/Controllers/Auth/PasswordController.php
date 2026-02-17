<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangedMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use App\Services\AuthService;

class PasswordController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }
    /**
     * Update the user's password.
     * Can be called either with /password (own password) or /users/{user}/password (user-scoped)
     */
    public function update(Request $request, ?User $user = null): RedirectResponse
    {
        // Determine which user's password to update
        $targetUser = $user ?? $request->user();

        // For security: only allow updating own password or if admin
        if ($targetUser->id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403, 'Unauthorized: You can only update your own password.');
        }

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        try {
            $this->authService->updatePassword($targetUser, $validated['password']);
            
            // Send password changed notification email
            Mail::queue(new PasswordChangedMail($targetUser));
            
            return back()->with('success', 'Votre mot de passe a ete mis a jour avec succes!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise a jour du mot de passe: ' . $e->getMessage());
        }
    }
}

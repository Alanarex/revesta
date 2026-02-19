<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\VerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function __construct(protected VerificationService $verificationService) {}

    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard.index', absolute: false));
        }

        try {
            $this->verificationService->sendVerification($request->user());

            return back()->with('success', 'Email de verification envoye avec succes!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'envoi de l\'email de verification: '.$e->getMessage());
        }
    }
}

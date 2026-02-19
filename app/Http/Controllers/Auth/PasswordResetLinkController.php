<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetLinkRequest;
use App\Services\PasswordService;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function __construct(protected PasswordService $passwordService) {}

    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(PasswordResetLinkRequest $request): RedirectResponse
    {
        try {
            $status = $this->passwordService->sendResetLink($request->input('email'));

            if ($status == PasswordBroker::RESET_LINK_SENT) {
                return back()->with('success', 'Lien de reinitialisation envoye avec succes! Veuillez verifier votre email.');
            }

            return back()->withInput($request->only('email'))
                ->with('error', 'Email non trouve ou erreur lors de l\'envoi du lien.');
        } catch (\Exception $e) {
            return back()->withInput($request->only('email'))
                ->with('error', 'Erreur lors de l\'envoi du lien de reinitialisation: '.$e->getMessage());
        }
    }
}

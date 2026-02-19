<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\PasswordService;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function __construct(protected PasswordService $passwordService) {}

    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        try {
            $status = $this->passwordService->reset($request->only('email', 'password', 'password_confirmation', 'token'));

            if ($status == PasswordBroker::PASSWORD_RESET) {
                return redirect()->route('login')->with('success', 'Votre mot de passe a ete reinitialise avec succes!');
            }

            return back()->withInput($request->only('email'))
                ->with('error', 'Erreur lors de la reinitialisation du mot de passe.');
        } catch (\Exception $e) {
            return back()->withInput($request->only('email'))
                ->with('error', 'Erreur lors de la reinitialisation du mot de passe: '.$e->getMessage());
        }
    }
}

@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
    <div class="card border-0">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 40px;">
            </div>

            @if (session('success'))
                <x-forms.alert type="success">
                    {{ session('success') }}
                </x-forms.alert>
            @endif

            @if (session('error'))
                <x-forms.alert type="error">
                    {{ session('error') }}
                </x-forms.alert>
            @endif

            @if (session('info'))
                <x-forms.alert type="info">
                    {{ session('info') }}
                </x-forms.alert>
            @endif

            @if (session('status') === 'account-deleted')
                <x-forms.alert type="info">
                    {{ __('Your account has been successfully deleted.') }}
                </x-forms.alert>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <x-inputs.email-input 
                    name="email" 
                    label="{{ __('Email') }}"
                    placeholder="exemple@email.com"
                    value=""
                    icon="fa-envelope"
                    required
                />

                <x-inputs.password-input 
                    name="password" 
                    label="{{ __('Mot de passe') }}"
                    placeholder="Entrez votre mot de passe"
                    icon="fa-lock"
                    required
                />

                <!-- Remember Me -->
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                    <label class="form-check-label text-dark" for="remember_me">
                        {{ __('Se souvenir de moi') }}
                    </label>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-4">
                    <div class="d-flex flex-sm-row gap-3">
                        @if (Route::has('password.request'))
                            <a class="small text-decoration-none" href="{{ route('password.request') }}">
                                {{ __('Mot de passe oublié ?') }}
                            </a>
                        @endif

                        @if (Route::has('register'))
                            <a class="small text-decoration-none" href="{{ route('register') }}">
                                {{ __('Créer un compte') }}
                            </a>
                        @endif
                    </div>

                    <x-buttons.button-primary text="{{ __('Se connecter') }}" />
                </div>

            </form>
        </div>
    </div>
@endsection

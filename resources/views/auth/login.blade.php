@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 40px;">
            </div>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Mot de passe') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                    <label class="form-check-label" for="remember_me">
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

                    <button type="submit" class="btn btn-primary">
                        {{ __('Se connecter') }}
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

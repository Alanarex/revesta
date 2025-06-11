@extends('layouts.guest')

@section('title', 'Inscription')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 40px;">
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- First Name -->
                <div class="mb-3">
                    <label for="first_name" class="form-label">Prénom</label>
                    <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror"
                        name="first_name" value="{{ old('first_name') }}" required autofocus autocomplete="given-name">
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Last Name -->
                <div class="mb-3">
                    <label for="last_name" class="form-label">Nom</label>
                    <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror"
                        name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name">
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <input id="password_confirmation" type="password"
                        class="form-control @error('password_confirmation') is-invalid @enderror"
                        name="password_confirmation" required autocomplete="new-password">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Footer Actions -->
                <div class="d-flex justify-content-between align-items-center">
                    <a class="text-decoration-none small" href="{{ route('login') }}">
                        Déjà inscrit ?
                    </a>

                    <button type="submit" class="btn btn-primary">
                        S'inscrire
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

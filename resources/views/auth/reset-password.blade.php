@extends('layouts.guest')

@section('title', 'Nouveau mot de passe')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 80px;">
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email', $request->email) }}" required autofocus
                        autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
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

                <!-- Submit -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

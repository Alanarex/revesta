@extends('layouts.guest')

@section('title', 'Réinitialisation du mot de passe')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 40px;">
            </div>

            <p class="text-muted small mb-4">
                Vous avez oublié votre mot de passe ? Aucun souci. Entrez votre adresse email ci-dessous et nous vous
                enverrons un lien de réinitialisation.
            </p>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        Envoyer le lien
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

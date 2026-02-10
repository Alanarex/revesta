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

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <x-inputs.text-input 
                    name="email" 
                    label="Adresse email"
                    type="email"
                    value="{{ old('email', $request->email) }}"
                    icon="fa-solid fa-envelope"
                    required
                />

                <x-inputs.text-input 
                    name="password" 
                    label="Nouveau mot de passe"
                    type="password"
                    icon="fa-solid fa-lock"
                    required
                />

                <x-inputs.text-input 
                    name="password_confirmation" 
                    label="Confirmer le mot de passe"
                    type="password"
                    icon="fa-solid fa-lock"
                    required
                />

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

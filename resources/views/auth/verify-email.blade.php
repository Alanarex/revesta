@extends('layouts.guest')

@section('title', 'Vérification de l’email')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 80px;">
            </div>

            <p class="text-muted small mb-4">
                Merci pour votre inscription ! Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le
                lien que nous venons de vous envoyer. Si vous ne l’avez pas reçu, nous vous en renverrons un avec plaisir.
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success">
                    Un nouveau lien de vérification a été envoyé à l’adresse email fournie lors de l’inscription.
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mt-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Renvoyer l’email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger text-decoration-none">
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

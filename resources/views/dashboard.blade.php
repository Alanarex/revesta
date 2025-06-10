@extends('layouts.app')

@section('title', $title . ' - ' . config('app.name'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h1 class="display-5 mb-3">{{ $header }}</h1>
            @include('partials.breadcrumbs')

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bon retour !</h5>
                    <p class="card-text">Ceci est votre tableau de bord principal. Vous pouvez y suivre l'activité,
                        consulter les rapports et gérer les paramètres.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

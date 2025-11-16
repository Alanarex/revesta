@extends('layouts.app')

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            @include('profile.partials.hero')

            @include('profile.partials.alerts')

            <!-- Content Row -->
            <div class="row g-4">
                <!-- Main Content -->
                <div class="col-12 col-xl-8">
                    @include('profile.partials.update-profile-form')

                    @include('profile.partials.update-password-form')

                    @include('profile.partials.delete-account-form')
                </div>

                @include('profile.partials.sidebar')
            </div>
        </div>
    </div>
@endsection

@include('profile.partials.scripts')
@include('profile.partials.styles')

@extends('layouts.email', ['title' => $title])

@section('content')
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Newsletter</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2 class="newsletter-title">{{ $title }}</h2>
            <div class="newsletter-body">
                {!! $content !!}
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        @include('emails.partials.footer', [
            'appName' => $appName,
            'appUrl' => $appUrl,
            'contactEmail' => $contactEmail,
            'unsubscribeUrl' => $unsubscribeUrl,
        ])
@endsection

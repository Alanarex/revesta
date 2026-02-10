@extends('admin.newsletters.layouts')


@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Breadcrumbs -->
            <div class="mb-4">
                <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
            </div>

            <!-- Status Banner -->
            <div class="mb-4">
                @if ($campaign->isDraft())
                    <div class="alert alert-warning mb-3">
                        <strong>Brouillon</strong> - Cette campagne n'a pas été envoyée
                    </div>
                @elseif ($campaign->isSent())
                    <div class="alert alert-success mb-3">
                        <strong>Envoyée</strong> - Campagne envoyée à {{ $campaign->sent_count }} destinataires le
                        {{ $campaign->sent_at->format('d/m/Y à H:i') }}
                    </div>
                @elseif ($campaign->isScheduled())
                    <div class="alert alert-info mb-3">
                        <strong>Programmée</strong> - Cette campagne sera envoyée le
                        {{ $campaign->scheduled_at->format('d/m/Y à H:i') }}
                    </div>
                @endif
            </div>

            <!-- Header with dropdown menu -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4>{{ $campaign->title }}</h4>
                    <small class="text-muted">Créée le {{ $campaign->created_at->format('d/m/Y à H:i') }}</small>
                </div>
                @if ($campaign->isDraft() || $campaign->isScheduled())
                    <div class="dropdown">
                        <button class="btn btn-link text-dark" type="button" data-bs-toggle="dropdown"
                            style="font-size: 1.5rem; padding: 0.5rem 0.75rem;">
                            <i class="fa fa-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if ($campaign->isDraft())
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.newsletters.edit', $campaign) }}">
                                        <i class="fa fa-pencil"></i> Éditer
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.newsletters.schedule-form', $campaign) }}">
                                        <i class="fa fa-clock"></i> Programmer
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item send-now-btn" href="#" data-campaign-id="{{ $campaign->id }}">
                                        <i class="fa fa-paper-plane"></i> Envoyer maintenant
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger delete-campaign-btn" href="#" data-campaign-id="{{ $campaign->id }}" data-campaign-title="{{ $campaign->title }}">
                                        <i class="fa fa-trash"></i> Supprimer
                                    </a>
                                </li>
                            @elseif ($campaign->isScheduled())
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.newsletters.schedule-form', $campaign) }}">
                                        <i class="fa fa-pencil"></i> Éditer la programmation
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item cancel-schedule-btn" href="#" data-campaign-id="{{ $campaign->id }}">
                                        <i class="fa fa-x-circle"></i> Annuler la programmation
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Email Template Preview -->
            <div class="border rounded newsletter-preview-container">
                <div class="newsletter-preview">
                    <!-- Email Header -->
                    <div class="newsletter-preview-header">
                        <h5 class="newsletter-preview-title">{{ $campaign->title }}</h5>
                        <small class="newsletter-preview-date">
                            Créée le {{ $campaign->created_at->format('d/m/Y') }} à
                            {{ $campaign->created_at->format('H:i') }}
                            @if ($campaign->sent_at)
                                | Envoyée le {{ $campaign->sent_at->format('d/m/Y') }} à
                                {{ $campaign->sent_at->format('H:i') }}
                            @endif
                        </small>
                    </div>

                    <!-- Email Content -->
                    <div class="newsletter-preview-body">
                        {!! $campaign->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/admin/newsletters/campaigns/show-handler.js')
    @endpush
@endsection

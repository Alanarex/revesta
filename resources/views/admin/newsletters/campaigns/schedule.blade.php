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
                        <strong>Brouillon</strong> - Programmez la date et l'heure d'envoi de cette campagne
                    </div>
                @elseif ($campaign->isScheduled())
                    <div class="alert alert-info mb-3">
                        <strong>Programmée</strong> - Modifier la date et l'heure d'envoi de cette campagne
                    </div>
                @endif
            </div>

            <div class="row g-4">
                <!-- Left Column: Preview -->
                <div class="col-lg-6">
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

                    <!-- Campaign Info -->
                    <div class="mt-4 p-3 rounded" style="background-color: white; border: 1px solid #ddd;">
                        <h6 class="fw-bold mb-3">Informations</h6>
                        <table class="table table-sm border-0 mb-0">
                            <tr>
                                <td class="fw-bold text-muted">Créée :</td>
                                <td>{{ $campaign->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Destinataires :</td>
                                <td>{{ $subscribersCount ?? 0 }} abonnés</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Right Column: Schedule Form -->
                <div class="col-lg-6">
                    <h6 class="fw-bold mb-3">{{ $campaign->isScheduled() ? 'Modifier la programmation' : 'Programmer l\'envoi' }}</h6>

                    <form method="POST" action="{{ route('admin.newsletters.schedule', $campaign) }}" id="scheduleForm">
                        @csrf

                        <x-inputs.date-input name="scheduled_date" label="Date d'envoi" :value="$campaign->isScheduled() ? $campaign->scheduled_at->format('Y-m-d') : now()->addDay()->format('Y-m-d')" required />

                        <x-inputs.time-input name="scheduled_time" label="Heure d'envoi" :value="$campaign->isScheduled() ? $campaign->scheduled_at->format('H:i') : '09:00'" required />

                        <div class="mb-4 p-3 rounded" style="background-color: #f8f9fa;">
                            <p class="small text-muted mb-2">
                                <i class="fa fa-info-circle"></i>
                                La campagne sera envoyée le :
                            </p>
                            <p class="fw-bold mb-0">
                                <span id="scheduledDateDisplay">-</span>
                            </p>
                        </div>

                        <div class="d-flex gap-2 justify-content-end pt-4">
                            <x-buttons.button-text :href="route('admin.newsletters.index')" text="Annuler" />
                            @if ($campaign->isScheduled())
                                <x-buttons.button-warning name="action_cancel_schedule" text="Annuler la programmation" />
                            @endif
                            <x-buttons.button-primary :text="$campaign->isScheduled() ? 'Mettre à jour' : 'Programmer'" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/admin/newsletters/campaigns/schedule-handler.js')
    @endpush
@endsection

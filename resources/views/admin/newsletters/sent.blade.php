@extends('admin.newsletters.layouts')

@section('content')
    <div class="container-xl">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h3>Campagnes envoyées</h3>
            </div>
        </div>

        <!-- Navigation -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('admin.newsletters.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-chevron-left"></i> Retour
                </a>
            </div>
        </div>

        <!-- Sent Campaigns List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%">Titre</th>
                                    <th width="15%">Envoyée le</th>
                                    <th width="15%">Destinataires</th>
                                    <th width="15%">Taux d'ouverture</th>
                                    <th width="20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sent as $campaign)
                                    <tr>
                                        <td>
                                            <div class="text-truncate">{{ $campaign->title }}</div>
                                            <small class="text-muted">{{ Str::limit(strip_tags($campaign->content), 60) }}</small>
                                        </td>
                                        <td>
                                            @if ($campaign->sent_at)
                                                {{ $campaign->sent_at->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $campaign->sent_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            @if ($campaign->sent_count > 0)
                                                @php
                                                    $opens = $campaign->logs()->where('opened', true)->count();
                                                    $rate = round(($opens / $campaign->sent_count) * 100, 1);
                                                @endphp
                                                <span class="badge bg-info">{{ $rate }}%</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" 
                                                        data-bs-target="#detailsModal{{ $campaign->id }}" 
                                                        title="Détails">
                                                    <i class="fa fa-eye"></i> Détails
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Aucune campagne envoyée
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if (method_exists($sent, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $sent->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Details Modals -->
    @foreach ($sent as $campaign)
        <div class="modal fade" id="detailsModal{{ $campaign->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $campaign->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <dl class="row">
                            <dt class="col-sm-4">Statut:</dt>
                            <dd class="col-sm-8"><span class="badge bg-success">Envoyée</span></dd>

                            <dt class="col-sm-4">Créée:</dt>
                            <dd class="col-sm-8">{{ $campaign->created_at->format('d/m/Y H:i') }}</dd>

                            <dt class="col-sm-4">Envoyée:</dt>
                            <dd class="col-sm-8">{{ $campaign->sent_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Destinataires:</dt>
                            <dd class="col-sm-8"><span class="badge bg-primary">{{ $campaign->sent_count ?? 0 }}</span></dd>

                            @php
                                $opens = $campaign->logs()->where('opened', true)->count();
                            @endphp
                            <dt class="col-sm-4">Ouvertures:</dt>
                            <dd class="col-sm-8"><span class="badge bg-info">{{ $opens }}</span></dd>

                            @php
                                $clicks = $campaign->logs()->where('clicked', true)->count();
                            @endphp
                            <dt class="col-sm-4">Clics:</dt>
                            <dd class="col-sm-8"><span class="badge bg-warning">{{ $clicks }}</span></dd>
                        </dl>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

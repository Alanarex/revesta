@extends('layouts.app')

@section('content')
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <h1 class="display-6 mb-2">{{ $header }}</h1>
            <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge text-bg-primary fs-6 px-3 py-2">
                {{ $isAdmin ? 'Vue administrateur' : 'Vue utilisateur' }}
            </span>
        </div>
    </div>

    @if ($isAdmin)
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small-box text-bg-primary h-100">
                    <div class="inner">
                        <h3>{{ number_format($dashboardData['cards']['users_total']) }}</h3>
                        <p>Utilisateurs</p>
                    </div>
                    <i class="small-box-icon fas fa-users"></i>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small-box text-bg-success h-100">
                    <div class="inner">
                        <h3>{{ number_format($dashboardData['cards']['users_verified_total']) }}</h3>
                        <p>Emails vérifiés</p>
                    </div>
                    <i class="small-box-icon fas fa-user-check"></i>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small-box text-bg-warning h-100">
                    <div class="inner">
                        <h3>{{ number_format($dashboardData['cards']['simulations_this_month']) }}</h3>
                        <p>Simulations (ce mois)</p>
                    </div>
                    <i class="small-box-icon fas fa-chart-line"></i>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small-box text-bg-danger h-100">
                    <div class="inner">
                        <h3>{{ number_format($dashboardData['cards']['blogs_pending_total']) }}</h3>
                        <p>Blogs en attente</p>
                    </div>
                    <i class="small-box-icon fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Simulations (30 derniers jours)</p>
                        <h4 class="mb-0">{{ number_format($dashboardData['cards']['simulations_last_30_days']) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Aides moyennes par simulation</p>
                        <h4 class="mb-0">{{ number_format($dashboardData['cards']['avg_aid_per_simulation'], 0, ',', ' ') }} €</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Gain énergétique moyen</p>
                        <h4 class="mb-0">{{ number_format($dashboardData['cards']['avg_energy_gain'], 2, ',', ' ') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-8">
                <div class="card h-100">
                    <div class="card-header border-0 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <h3 class="card-title fw-semibold mb-0">Croissance utilisateurs & simulations</h3>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Période admin dashboard">
                            <button type="button" class="btn btn-outline-primary active js-admin-range-btn" data-range="7">7j</button>
                            <button type="button" class="btn btn-outline-primary js-admin-range-btn" data-range="30">30j</button>
                            <button type="button" class="btn btn-outline-primary js-admin-range-btn" data-range="90">90j</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="adminGrowthChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h3 class="card-title fw-semibold mb-0">Répartition des blogs</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="adminBlogStatusChart" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-7">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h3 class="card-title fw-semibold mb-0">Carte des utilisateurs (géolocalisés)</h3>
                    </div>
                    <div class="card-body">
                        <div id="adminUsersMap" style="height: 360px; border-radius: 0.75rem;"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h3 class="card-title fw-semibold mb-0">Aperçu global</h3>
                    </div>
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Simulations ce mois</span>
                            <strong>{{ number_format($dashboardData['cards']['simulations_this_month']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Blogs publiés</span>
                            <strong>{{ number_format($dashboardData['cards']['blogs_published_total']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Commentaires blogs</span>
                            <strong>{{ number_format($dashboardData['cards']['comments_total']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Notifications non lues</span>
                            <strong>{{ number_format($dashboardData['cards']['unread_notifications_total']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Montant aides simulées</span>
                            <strong>{{ number_format($dashboardData['cards']['aid_amount_total'], 0, ',', ' ') }} €</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-8">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h3 class="card-title fw-semibold mb-0">Valeur des simulations (6 mois)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="adminSimulationValueChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h3 class="card-title fw-semibold mb-0">Dernières simulations</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Montant</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dashboardData['recent_simulations'] as $simulation)
                                        <tr>
                                            <td>{{ $simulation->user?->full_name ?? trim(($simulation->user?->first_name ?? '') . ' ' . ($simulation->user?->last_name ?? '')) }}</td>
                                            <td>{{ number_format((float) $simulation->montant_total_aides, 0, ',', ' ') }} €</td>
                                            <td>{{ $simulation->created_at?->format('d/m') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Aucune simulation récente.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-semibold mb-0">Blogs en attente de modération</h3>
                <a href="{{ route('admin.blogs.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dashboardData['pending_blogs'] as $blog)
                                <tr>
                                    <td>{{ $blog->title }}</td>
                                    <td>{{ $blog->user?->full_name ?? trim(($blog->user?->first_name ?? '') . ' ' . ($blog->user?->last_name ?? '')) }}</td>
                                    <td>{{ $blog->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.blogs.show', $blog) }}" class="btn btn-sm btn-outline-secondary">Ouvrir</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Aucun blog en attente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small-box text-bg-primary h-100">
                    <div class="inner">
                        <h3>{{ number_format($dashboardData['cards']['my_simulations_this_month']) }}</h3>
                        <p>Mes simulations (ce mois)</p>
                    </div>
                    <i class="small-box-icon fas fa-chart-line"></i>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small-box text-bg-success h-100">
                    <div class="inner">
                        <h3>{{ number_format($dashboardData['cards']['my_avg_aid_per_simulation'], 0, ',', ' ') }} €</h3>
                        <p>Aides moyennes / simulation</p>
                    </div>
                    <i class="small-box-icon fas fa-euro-sign"></i>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small-box text-bg-warning h-100">
                    <div class="inner">
                        <h3>{{ number_format($dashboardData['cards']['my_best_aid_amount'], 0, ',', ' ') }} €</h3>
                        <p>Meilleure simulation</p>
                    </div>
                    <i class="small-box-icon fas fa-bookmark"></i>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small-box text-bg-danger h-100">
                    <div class="inner">
                        <h3>{{ number_format($dashboardData['cards']['my_avg_energy_gain'], 2, ',', ' ') }}</h3>
                        <p>Gain énergétique moyen</p>
                    </div>
                    <i class="small-box-icon fas fa-bell"></i>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Mes simulations totales</p>
                        <h4 class="mb-0">{{ number_format($dashboardData['cards']['my_simulations_total']) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Aides estimées cumulées</p>
                        <h4 class="mb-0">{{ number_format($dashboardData['cards']['my_aid_amount_total'], 0, ',', ' ') }} €</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Notifications non lues</p>
                        <h4 class="mb-0">{{ number_format($dashboardData['cards']['my_unread_notifications_total']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-8">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h3 class="card-title fw-semibold mb-0">Mon activité simulations</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="userSimulationChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h3 class="card-title fw-semibold mb-0">Mes blogs</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="userBlogStatusChart" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-8">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h3 class="card-title fw-semibold mb-0">Valeur de mes simulations (6 mois)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="userSimulationValueChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center">
                        <h3 class="card-title fw-semibold mb-0">Mes dernières simulations</h3>
                        <a href="{{ route('simulations.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Montant</th>
                                        <th>Gain</th>
                                        <th class="text-end">Détail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dashboardData['my_recent_simulations'] as $simulation)
                                        <tr>
                                            <td>{{ number_format((float) $simulation->montant_total_aides, 0, ',', ' ') }} €</td>
                                            <td>{{ number_format((float) $simulation->gain_energetique, 2, ',', ' ') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('simulations.show', $simulation) }}" class="btn btn-xs btn-outline-secondary">Ouvrir</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Aucune simulation.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-semibold mb-0">Derniers blogs à lire</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse ($dashboardData['latest_blogs_to_read'] as $blog)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card border h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-2">{{ $blog->title }}</h5>
                                    <p class="text-muted small mb-2">
                                        Par {{ $blog->user?->full_name ?? trim(($blog->user?->first_name ?? '') . ' ' . ($blog->user?->last_name ?? '')) }}
                                        · {{ $blog->published_at?->diffForHumans() }}
                                    </p>
                                    <p class="card-text text-muted mb-3">{{ \Illuminate\Support\Str::limit($blog->short_description, 120) }}</p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">
                                            <i class="fas fa-comment"></i> {{ $blog->comments_count }}
                                            <i class="fas fa-heart ms-2"></i> {{ $blog->likes_count }}
                                        </span>
                                        <a href="{{ route('blogs.show', $blog) }}" class="btn btn-sm btn-primary">Lire</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light border text-muted mb-0">Aucun blog récent disponible.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <script id="dashboard-data" type="application/json">
        @json($dashboardData)
    </script>
@endsection

@push('scripts')
    @vite('resources/js/dashboard/index.js')
@endpush

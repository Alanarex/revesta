@extends('admin.newsletters.layouts')

@section('content')
    <div class="container-xl">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h3>Brouillons</h3>
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

        <!-- Drafts List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40%">Titre</th>
                                    <th width="20%">Créée le</th>
                                    <th width="15%">Modifiée le</th>
                                    <th width="25%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($drafts as $draft)
                                    <tr>
                                        <td>
                                            <div class="text-truncate">{{ $draft->title }}</div>
                                            <small
                                                class="text-muted">{{ Str::limit(strip_tags($draft->content), 60) }}</small>
                                        </td>
                                        <td>
                                            {{ $draft->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            {{ $draft->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.newsletters.edit', $draft) }}"
                                                    class="btn btn-outline-primary">
                                                    <i class="fa fa-pencil"></i> Éditer
                                                </a>
                                                <a href="{{ route('admin.newsletters.schedule-form', $draft) }}"
                                                    class="btn btn-outline-info">
                                                    <i class="fa fa-clock"></i> Programmer
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.newsletters.send-now', $draft) }}"
                                                    style="display: inline;"
                                                    onsubmit="return confirm('Êtes-vous sûr? Cette action ne peut pas être annulée.');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success"
                                                        title="Envoyer maintenant">
                                                        <i class="fa fa-send"></i> Envoyer
                                                    </button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('admin.newsletters.destroy', $draft) }}"
                                                    style="display: inline;" onsubmit="return confirm('Êtes-vous sûr?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fa fa-trash"></i> Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Aucun brouillon
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if (method_exists($drafts, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $drafts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/admin/newsletters/campaigns/show-handler.js')
    @endpush
@endsection

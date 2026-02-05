@extends('admin.addresses.layouts')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Modifier l'utilisateur</h3>
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Retour</a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required />
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nom</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required />
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required />
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" />
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Rôle</label>
                        <select name="role_id" class="form-select">
                            <option value="">-- Aucun --</option>
                            @foreach(\App\Models\Role::all() as $role)
                                <option value="{{ $role->id }}" {{ (int) old('role_id', $user->role_id) === $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Adresse</label>
                        @if($user->address)
                            <div>
                                <a href="{{ route('admin.addresses.edit', $user->address) }}">{{ $user->address->label }}</a>
                                <div class="small text-muted">{{ $user->address->street }} — {{ $user->address->postal_code }} {{ $user->address->city }}</div>
                            </div>
                        @else
                            <div class="text-muted">Aucune adresse liée</div>
                        @endif
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <button type="button" class="btn btn-warning" onclick="alert('Reset password (placeholder)')">Réinitialiser le mot de passe</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

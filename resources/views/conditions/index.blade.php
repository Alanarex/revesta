@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Conditions par aide</h1>

        {{-- Display validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Des erreurs sont survenues :</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('conditions.update') }}">
            @csrf

            @foreach ($conditionsGrouped as $aidId => $conditions)
                <h4>{{ $conditions->first()->aid->name }}</h4>
                <table class="table table-bordered mb-5">
                    <thead>
                        <tr>
                            <th>Modèle</th>
                            <th>Attribut</th>
                            <th>Opérateur</th>
                            <th>Valeur</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($conditions as $condition)
                            <tr>
                                <td>
                                    <input type="hidden" name="conditions[{{ $aidId }}][{{ $condition->id }}][aid_id]"
                                        value="{{ $aidId }}">
                                    <select name="conditions[{{ $aidId }}][{{ $condition->id }}][model]"
                                        class="form-select model-select">
                                        @foreach ($models as $key => $model)
                                            <option value="{{ $key }}"
                                                {{ $key === $condition->model ? 'selected' : '' }}>
                                                {{ $model['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="conditions[{{ $aidId }}][{{ $condition->id }}][attribute]"
                                        class="form-select attribute-select">
                                        @foreach ($models[$condition->model]['attributes'] as $attr => $label)
                                            <option value="{{ $attr }}"
                                                {{ $condition->attribute === $attr ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="conditions[{{ $aidId }}][{{ $condition->id }}][operator]"
                                        class="form-select">
                                        @foreach ($operators as $op)
                                            <option value="{{ $op }}"
                                                {{ $op === $condition->operator ? 'selected' : '' }}>
                                                {{ $op }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                @php
                                    $type = $models[$condition->model]['types'][$condition->attribute] ?? 'text';
                                    $value = $condition->value;
                                    $name = "conditions[{$aidId}][{$condition->id}][value]";
                                @endphp
                                <td>
                                    @if ($type === 'select')
                                        <select name="{{ $name }}" class="form-select">
                                            @foreach ($models[$condition->model]['options'] ?? [] as $optionValue => $label)
                                                <option value="{{ $optionValue }}"
                                                    {{ $optionValue == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="{{ $type }}" name="{{ $name }}"
                                            class="form-control value-input" value="{{ $value }}">
                                    @endif
                                </td>
                                <td class="text-center align-middle p-1" style="border: none;">
                                    <button type="button" class="btn p-0 m-0 bg-transparent text-danger remove-row">
                                        <i class="fa-solid fa-xmark fa-lg"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="add-row-line">
                            <td colspan="4" class="position-relative text-center p-0">
                                <hr class="m-0">
                            </td>
                            <td class="position-relative text-center p-0">
                                <button type="button" class="btn p-0 m-0 border-0 bg-transparent text-primary add-row"
                                    data-aid-id="{{ $aidId }}">
                                    <i class="fas fa-plus-circle fa-lg plus-icon"></i>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @endforeach

            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
    </div>
@endsection

@include('conditions.partials.scripts')
@include('conditions.partials.styles')

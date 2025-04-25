@extends('layouts.app')

@section('content')
    <div class="container py-4 text-white">
        <h1 class="mb-4">Crea Nuovo Servizio</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Attenzione!</strong> Correggi gli errori sotto.
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.services.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nome Servizio</label>
                <input type="text" name="name" id="name" class="form-control" required
                       value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Prezzo (€)</label>
                <input type="number" step="0.01" min="0" name="price" id="price" class="form-control" required
                       value="{{ old('price') }}">
            </div>

            <div class="mb-3">
                <label for="percentage" class="form-label">Percentuale Operatore (%)</label>
                <input type="number" step="1" min="0" max="100" name="percentage" id="percentage" class="form-control" required
                       value="{{ old('percentage', 0) }}">
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" id="is_variable_price" name="is_variable_price"
                    {{ old('is_variable_price') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_variable_price">Prezzo Variabile</label>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-primary">Crea Servizio</button>
            </div>
        </form>
    </div>
@endsection

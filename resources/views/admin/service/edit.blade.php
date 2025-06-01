@extends('layouts.app')

@section('content')
    <div class="container py-4 text-white">
        <h1 class="mb-4">Modifica Servizio</h1>

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

        <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nome Servizio</label>
                <input type="text" name="name" id="name" class="form-control" required
                       value="{{ old('name', $service->name) }}">
            </div>

            @php
                $isAbbonamento = strtolower($service->name) === 'abbonamento';
            @endphp

            <div class="mb-3">
                <label for="price" class="form-label">Prezzo (€)</label>
                <input type="number" step="0.01" min="0" name="price" id="price"
                       class="form-control {{ $isAbbonamento ? 'bg-light text-muted' : '' }}"
                       value="{{ old('price', $service->price) }}"
                       {{ $isAbbonamento ? 'readonly disabled' : '' }}>
                @if ($isAbbonamento)
                    <small class="text-warning">Il prezzo per “Abbonamento” è fisso e non modificabile.</small>
                @endif
            </div>

            <div class="mb-3">
                <label for="percentage" class="form-label">Percentuale Operatore (%)</label>
                <input type="number" step="1" min="0" max="100" name="percentage" id="percentage" class="form-control" required
                       value="{{ old('percentage', $service->percentage) }}">
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="is_variable_price" name="is_variable_price"
                    {{ old('is_variable_price', $service->is_variable_price) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_variable_price">Prezzo Variabile</label>
                <div id="var_price_conflict" class="text-warning small d-none">
                    Non compatibile con "Usa Quantità".
                </div>
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" id="uses_quantity" name="uses_quantity"
                    {{ old('uses_quantity', $service->uses_quantity) ? 'checked' : '' }}>
                <label class="form-check-label" for="uses_quantity">Usa Quantità</label>
                <div id="qty_conflict" class="text-warning small d-none">
                    Non compatibile con "Prezzo Variabile".
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const varPrice = document.getElementById('is_variable_price');
        const usesQty = document.getElementById('uses_quantity');
        const varConflict = document.getElementById('var_price_conflict');
        const qtyConflict = document.getElementById('qty_conflict');

        function toggleConflicts() {
            const conflict = varPrice.checked && usesQty.checked;

            if (conflict) {
                varConflict.classList.remove('d-none');
                qtyConflict.classList.remove('d-none');
            } else {
                varConflict.classList.add('d-none');
                qtyConflict.classList.add('d-none');
            }
        }

        varPrice.addEventListener('change', toggleConflicts);
        usesQty.addEventListener('change', toggleConflicts);
        toggleConflicts(); // initial state
    });
</script>
@endpush

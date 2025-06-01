@extends('layouts.app')

@section('content')
    <div class="container py-4 text-white">
        <h1 class="mb-4">Modifica prestazione</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Attenzione!</strong> Correggi gli errori sotto.
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.service-logs.update', $serviceLog->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Cliente --}}
            <div class="mb-3">
                <label for="client_id" class="form-label">Cliente</label>
                <select name="client_id" id="client_id" class="form-select" required>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}"
                            {{ old('client_id', $serviceLog->client_id) == $client->id ? 'selected' : '' }}>
                            {{ $client->last_name }} {{ $client->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Servizio --}}
            <div class="mb-3">
                <label for="service_id" class="form-label">Servizio</label>
                <select name="service_id" id="service_id" class="form-select" required>
                    <option value="">-- Seleziona un servizio --</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}"
                            data-name="{{ strtolower($service->name) }}"
                            data-variable="{{ $service->is_variable_price }}"
                            {{ old('service_id', $serviceLog->service_id) == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                            ({{ $service->price ? '€' . number_format($service->price, 2, ',', '.') : 'Prezzo da definire' }})
                        </option>
                    @endforeach
                </select>
            </div>

            @php
                $isAdmin = auth()->user()->role === 'admin';
                $selectedServiceId = old('service_id', $serviceLog->service_id);
                $selectedService = $services->firstWhere('id', $selectedServiceId);
                $serviceName = strtolower($selectedService?->name ?? '');
                $isAbbonamento = $serviceName === 'abbonamento';
                $isExtension = $serviceName === 'extensions';
                $hasQuantity = $serviceLog->quantity !== null || $isExtension;
            @endphp

            {{-- Quantity for Extensions --}}
            @if ($hasQuantity)
                <div class="mb-3">
                    <label for="quantity" class="form-label">Numero di ciocche</label>
                    <input
                        type="number"
                        name="quantity"
                        id="quantity"
                        class="form-control"
                        step="1"
                        min="0"
                        value="{{ old('quantity', $serviceLog->quantity) }}"
                        placeholder="Numero di ciocche"
                    >
                </div>
            @endif

            {{-- Custom Price (Always shown) --}}
<div class="mb-3">
    <label for="custom_price" class="form-label">Prezzo personalizzato</label>
    <input
        type="number"
        name="custom_price"
        id="custom_price"
        class="form-control"
        step="0.01"
        min="0"
        value="{{ old('custom_price', $serviceLog->custom_price) }}"
        placeholder="Prezzo personalizzato"
        {{ (!$isAdmin && $isAbbonamento) ? 'readonly disabled' : '' }}
    >
    @if (!$isAdmin && $isAbbonamento)
        <small class="text-white">Il prezzo per questo servizio è fisso e non modificabile.</small>
    @endif
</div>


            {{-- Note --}}
            <div class="mb-3">
                <label for="notes" class="form-label">Note</label>
                <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $serviceLog->notes) }}</textarea>
            </div>

            {{-- Data --}}
            <div class="mb-3">
                <label for="performed_at" class="form-label">Data e ora</label>
                <input type="datetime-local" name="performed_at" id="performed_at" class="form-control"
                    value="{{ old('performed_at', \Carbon\Carbon::parse($serviceLog->performed_at)->format('Y-m-d\TH:i')) }}"
                    required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.service-logs.index') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-primary">Salva modifiche</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#client_id, #service_id').select2({
            width: '100%',
            language: {
                noResults: () => 'Nessun risultato trovato',
                searching: () => 'Ricerca in corso...',
                inputTooShort: () => 'Inserisci più caratteri'
            },
            placeholder: 'Seleziona un’opzione',
            allowClear: true
        });
    });
</script>
@endpush

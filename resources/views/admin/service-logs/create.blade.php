@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-white">Registra una nuova prestazione</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Attenzione!</strong> Correggi gli errori sotto.<br><br>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.service-logs.store') }}" method="POST">
        @csrf

        {{-- CLIENTE --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Seleziona Cliente</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createClientModal">
                    + Nuovo Cliente
                </button>
            </div>
            <div class="card-body">
                <select name="client_id" id="client_id" class="form-select" required>
                    <option value="">-- Seleziona un cliente --</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->last_name }} {{ $client->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- SERVIZI --}}
        <div class="card mb-4">
            <div class="card-header">Servizi Erogati</div>
            <div class="card-body">
                <div class="row">
                    @foreach ($services as $service)
                        @php
                            $isAbbonamento = str_contains(strtolower($service->name), 'abbonamento');
                            $isExtension = strtolower($service->name) === 'extensions';
                            $isAdmin = auth()->user()->role === 'admin';
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check mb-1">
                                <input type="checkbox" class="form-check-input service-checkbox"
                                    name="service_ids[]" value="{{ $service->id }}"
                                    id="service_{{ $service->id }}"
                                    {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="service_{{ $service->id }}">
                                    {{ $service->name }} 
                                    ({{ $service->price ? '€' . number_format($service->price, 2, ',', '.') : 'Prezzo da definire' }})
                                </label>
                            </div>

                            @if ($isExtension || $service->is_variable_price)
                                <div class="mt-1">
                                    <input
                                        type="number"
                                        class="form-control form-control-sm variable-price"
                                        data-related-checkbox="service_{{ $service->id }}"
                                        name="custom_prices[{{ $service->id }}]"
                                        placeholder="{{ $isExtension ? 'Numero di ciocche' : 'Prezzo del prodotto' }}"
                                        value="{{ old('custom_prices.' . $service->id, (!$isAdmin && $isAbbonamento) ? 0 : '') }}"
                                        style="display: none;"
                                        min="0"
                                        step="{{ $isExtension ? '1' : '0.01' }}"
                                        @if (!$isAdmin && $isAbbonamento) disabled readonly @endif
                                    >

                                    @if (!$isAdmin && $isAbbonamento)
                                        <small class="text-muted d-block">
                                            Il prezzo per questo servizio è fisso e non modificabile.
                                        </small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- DATA --}}
        <div class="card mb-4">
            <div class="card-header">Data e Ora della Prestazione</div>
            <div class="card-body">
                <input type="datetime-local" name="performed_at" id="performed_at" class="form-control"
                    value="{{ old('performed_at', now()->format('Y-m-d\TH:i')) }}" required>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.service-logs.index') }}" class="btn btn-secondary">Annulla</a>
            <button type="submit" class="btn btn-primary">Registra</button>
        </div>
    </form>
</div>

{{-- MODAL CLIENTE --}}
@include('admin.service-logs._client-modal')
@endsection

@push('scripts')
<script>
    const IS_ADMIN = @json(auth()->user()->role === 'admin');

    document.addEventListener('DOMContentLoaded', function () {
        $('#client_id').select2({
            placeholder: 'Cerca un cliente...',
            width: '100%',
            language: 'it'
        });

        document.querySelectorAll('.service-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const id = this.id;
                const relatedInputs = document.querySelectorAll(`.variable-price[data-related-checkbox="${id}"]`);

                relatedInputs.forEach(input => {
                    input.style.display = this.checked ? 'block' : 'none';
                    if (!this.checked) {
                        input.value = '';
                    }
                });
            });

            checkbox.dispatchEvent(new Event('change'));
        });

        @if ($errors->any() && old('_from_modal'))
            const modal = new bootstrap.Modal(document.getElementById('createClientModal'));
            modal.show();
        @endif

        const newClientId = @json(session('new_client_id'));
        if (newClientId) {
            $('#client_id').val(newClientId).trigger('change');
            document.getElementById('client_id').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/it.js"></script>
@endpush

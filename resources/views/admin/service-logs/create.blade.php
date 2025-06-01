@extends('layouts.app')

@section('content')
    <div class="container py-4 text-white">
        <h1 class="mb-4">Registra una nuova prestazione</h1>

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
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                        data-bs-target="#createClientModal">
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
                                $isAdmin = auth()->user()->role === 'admin';
                                $isAbbonamento = strtolower($service->name) === 'abbonamento';
                                $hasOld = old("services.$service->id.id") == $service->id;
                            @endphp

                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input service-toggle"
                                        id="service_{{ $service->id }}" name="services[{{ $service->id }}][id]"
                                        value="{{ $service->id }}" data-index="{{ $service->id }}"
                                        data-uses-quantity="{{ $service->uses_quantity }}"
                                        data-is-variable="{{ $service->is_variable_price }}"
                                        data-is-abbonamento="{{ $isAbbonamento ? '1' : '0' }}" {{ $hasOld ? 'checked' : '' }}>

                                    <label class="form-check-label" for="service_{{ $service->id }}">
                                        {{ $service->name }}
                                        ({{ $service->price ? '€' . number_format($service->price, 2, ',', '.') : 'Prezzo da definire' }})
                                    </label>
                                </div>

                                {{-- Optional Input --}}
                                @if ($service->uses_quantity || $service->is_variable_price || $isAbbonamento)
                                    <div class="mt-2 service-entry-input" id="input_{{ $service->id }}" style="display: none;">
                                        @if ($service->uses_quantity)
                                            <input type="number" class="form-control form-control-sm"
                                                name="services[{{ $service->id }}][entry]" placeholder="Quantità" min="1" step="1"
                                                value="{{ old("services.$service->id.entry") }}" disabled>
                                        @elseif ($service->is_variable_price || $isAbbonamento)
                                            <input type="number" class="form-control form-control-sm"
                                                name="services[{{ $service->id }}][entry]" placeholder="Prezzo personalizzato (€)"
                                                min="0" step="0.01"
                                                value="{{ old("services.$service->id.entry", $isAbbonamento && !$isAdmin ? 0 : '') }}"
                                                {{ $isAbbonamento && !$isAdmin ? 'readonly' : '' }} disabled>

                                            @if ($isAbbonamento && !$isAdmin)
                                                <small class="text-muted">Il prezzo per questo servizio è fisso e non modificabile.</small>
                                            @endif
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

    @include('admin.service-logs._client-modal')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.service-toggle').forEach(checkbox => {
                const serviceId = checkbox.dataset.index;
                const inputContainer = document.getElementById(`input_${serviceId}`);

                if (!inputContainer) return;

                const toggleInputs = (enable) => {
                    inputContainer.style.display = enable ? 'block' : 'none';
                    inputContainer.querySelectorAll('input').forEach(input => {
                        input.disabled = !enable;
                        if (!enable && !input.readOnly) input.value = '';
                    });
                };

                // Initial load
                toggleInputs(checkbox.checked);

                checkbox.addEventListener('change', () => {
                    toggleInputs(checkbox.checked);
                });
            });
        });
    </script>
@endpush
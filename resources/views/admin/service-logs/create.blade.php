@extends('layouts.app')

@section('content')
    <div class="container py-4">
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

            {{-- BLOCCO CLIENTE --}}
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
                                {{ $client->first_name }} {{ $client->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- BLOCCO SERVIZI --}}
            <div class="card mb-4">
                <div class="card-header">Servizi Erogati</div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($services as $service)
                            <div class="col-md-6 col-lg-4 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        name="service_ids[]" value="{{ $service->id }}"
                                        id="service_{{ $service->id }}"
                                        {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="service_{{ $service->id }}">
                                        {{ $service->name }} ({{ $service->price ? '€' . number_format($service->price, 2, ',', '.') : 'Prezzo da definire' }})
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- BLOCCO DATA --}}
            <div class="card mb-4">
                <div class="card-header">Data e Ora della Prestazione</div>
                <div class="card-body">
                    <input type="datetime-local" name="performed_at" id="performed_at" class="form-control"
                        value="{{ old('performed_at') ?? now()->format('Y-m-d\TH:i') }}" required>
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
        document.addEventListener('DOMContentLoaded', function () {
            // Inizializza Select2 con localizzazione italiana
            $('#client_id').select2({
                placeholder: 'Cerca un cliente...',
                width: '100%',
                language: 'it'
            });

            // Preseleziona il cliente creato da modale, se presente
            const newClientId = @json(session('new_client_id'));
            if (newClientId) {
                $('#client_id').val(newClientId).trigger('change');
                document.getElementById('client_id').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Mostra il modal se c’erano errori
            @if ($errors->any() && old('_from_modal'))
                const modal = new bootstrap.Modal(document.getElementById('createClientModal'));
                modal.show();
            @endif
        });
    </script>

    {{-- Script lingua italiana per Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/it.js"></script>
@endpush

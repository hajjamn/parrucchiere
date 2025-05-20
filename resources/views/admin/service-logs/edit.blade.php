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
                        <option value="{{ $client->id }}" {{ old('client_id', $serviceLog->client_id) == $client->id ? 'selected' : '' }}>
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
                        <option value="{{ $service->id }}" data-variable="{{ $service->is_variable_price ? '1' : '0' }}"
                            data-type="{{ strtolower($service->name) }}" {{ old('service_id', $serviceLog->service_id) == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                            ({{ $service->price ? '€' . number_format($service->price, 2, ',', '.') : 'Prezzo da definire' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Custom price --}}
            <div class="mb-3" id="customPriceWrapper" style="display: none;">
                <label for="custom_price" class="form-label" id="customPriceLabel">Prezzo personalizzato</label>
                {{-- <input type="number" name="custom_price" id="custom_price" step="0.01" min="0" class="form-control"
                    value="{{ old('custom_price', $serviceLog->custom_price) }}"> --}}

                {{-- Conditional for Abbonamento --}}
                @php
                    $isAdmin = auth()->user()->role === 'admin';
                    $selectedService = $services->firstWhere('id', old('service_id', $serviceLog->service_id));
                    $isAbbonamento = strtolower($selectedService?->name ?? '') === 'abbonamento';
                @endphp

                @if ($isAdmin || !$isAbbonamento)
                    <input type="number" name="custom_price" id="custom_price" step="0.01" min="0" class="form-control"
                        value="{{ old('custom_price', $serviceLog->custom_price) }}">
                @else
                    <input type="text" class="form-control" value="€{{ number_format($serviceLog->custom_price, 2, ',', '.') }}"
                        readonly>
                @endif

            </div>

            {{-- Note --}}
            <div class="mb-3">
                <label for="notes" class="form-label">Note</label>
                <textarea name="notes" id="notes" rows="3"
                    class="form-control">{{ old('notes', $serviceLog->notes) }}</textarea>
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
        const IS_ADMIN = @json(auth()->user()->role === 'admin');
    </script>

    <script>
        $(document).ready(function () {
            $('#client_id, #service_id').select2({
                width: '100%',
                language: 'it',
                placeholder: 'Seleziona un’opzione',
                allowClear: true
            });

            /* function updateCustomPriceField() {
                const selected = $('#service_id option:selected');
                const isVariable = selected.data('variable') == 1;
                const type = String(selected.data('type')).toLowerCase().trim();

                if (isVariable) {
                    $('#customPriceWrapper').show();

                    if (type.includes('extension')) {
                        $('#customPriceLabel').text('Numero di ciocche');
                        $('#custom_price').attr('step', 1).attr('placeholder', 'Numero di ciocche');
                    } else if (type.includes('vendita')) {
                        $('#customPriceLabel').text('Prezzo del prodotto');
                        $('#custom_price').attr('step', 0.01).attr('placeholder', 'Prezzo del prodotto');
                    } else {
                        $('#customPriceLabel').text('Prezzo personalizzato');
                        $('#custom_price').attr('step', 0.01).attr('placeholder', 'Prezzo personalizzato');
                    }
                } else {
                    $('#customPriceWrapper').hide();
                    $('#custom_price').val('');
                }
            } */

            function updateCustomPriceField() {
                const selected = $('#service_id option:selected');
                const isVariable = selected.data('variable') == 1;
                const type = String(selected.data('type')).toLowerCase().trim();

                const isAbbonamento = type.includes('abbonamento');

                if (isVariable && (IS_ADMIN || !isAbbonamento)) {
                    $('#customPriceWrapper').show();

                    if (type.includes('extension')) {
                        $('#customPriceLabel').text('Numero di ciocche');
                        $('#custom_price').attr('step', 1).attr('placeholder', 'Numero di ciocche').prop('readonly', false);
                    } else if (type.includes('vendita')) {
                        $('#customPriceLabel').text('Prezzo del prodotto');
                        $('#custom_price').attr('step', 0.01).attr('placeholder', 'Prezzo del prodotto').prop('readonly', false);
                    } else {
                        $('#customPriceLabel').text('Prezzo personalizzato');
                        $('#custom_price').attr('step', 0.01).attr('placeholder', 'Prezzo personalizzato').prop('readonly', false);
                    }

                } else if (isAbbonamento && !IS_ADMIN) {
                    $('#customPriceWrapper').show();
                    $('#customPriceLabel').text('Prezzo abbonamento');
                    $('#custom_price').prop('readonly', true);
                } else {
                    $('#customPriceWrapper').hide();
                    $('#custom_price').val('');
                }
            }


            $('#service_id').on('change', updateCustomPriceField);
            $('#service_id').trigger('change'); // forza aggiornamento all'apertura
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/it.js"></script>
@endpush
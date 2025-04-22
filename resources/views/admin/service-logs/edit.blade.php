@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Modifica prestazione</h1>

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

        <form action="{{ route('admin.service-logs.update', $serviceLog->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="client_id" class="form-label">Cliente</label>
                <select name="client_id" id="client_id" class="form-select" required>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $serviceLog->client_id) == $client->id ? 'selected' : '' }}>
                            {{ $client->first_name }} {{ $client->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="service_id" class="form-label">Servizio</label>
                <select name="service_id" id="service_id" class="form-select" required>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id', $serviceLog->service_id) == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} (€{{ number_format($service->price, 2, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="performed_at" class="form-label">Data e ora</label>
                <input type="datetime-local" name="performed_at" id="performed_at" class="form-control"
                    value="{{ old('performed_at', \Carbon\Carbon::parse($serviceLog->performed_at)->format('Y-m-d\TH:i')) }}"
                    required>
            </div>

            <button type="submit" class="btn btn-primary">Salva modifiche</button>
            <a href="{{ route('admin.service-logs.index') }}" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
@endsection
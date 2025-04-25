@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 text-white">Dettagli Servizio</h1>

        <div class="card mb-4">
            <div class="card-body">
                <h4 class="mb-2">{{ $service->name }}</h4>
                <p class="mb-1"><strong>Prezzo:</strong>
                    {{ $service->price ? '€' . number_format($service->price, 2, ',', '.') : 'Prezzo da definire' }}
                </p>
                <p class="mb-1"><strong>Percentuale Operatore:</strong> {{ $service->percentage }}%</p>
                <p class="mb-0"><strong>Prezzo Variabile:</strong> {{ $service->is_variable_price ? 'Sì' : 'No' }}</p>
            </div>
        </div>

        <h5 class="text-white">Prestazioni con questo servizio</h5>

        @if ($service->serviceLogs->isEmpty())
            <div class="alert alert-info">Nessuna prestazione trovata per questo servizio.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Operatore</th>
                            <th>Data</th>
                            <th>Prezzo</th>
                            <th>Commissione</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($service->serviceLogs->sortByDesc('performed_at') as $log)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.clients.show', $log->client_id) }}">
                                                {{ $log->client->first_name }} {{ $log->client->last_name }}
                                            </a>
                                        </td>
                                        <td>{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($log->performed_at)->translatedFormat('d/m/Y H:i') }}</td>
                                        <td>
                                            €{{ number_format($log->custom_price ?? $log->service->price ?? 0, 2, ',', '.') }}
                                        </td>
                                        <td>
                                            €{{ number_format(
                                ($log->custom_price ?? $log->service->price ?? 0) * ($log->service->percentage / 100),
                                2,
                                ',',
                                '.'
                            ) }}
                                        </td>
                                    </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary mt-3">Torna ai servizi</a>
    </div>
@endsection
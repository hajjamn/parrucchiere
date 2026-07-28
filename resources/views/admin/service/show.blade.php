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
                <p class="mb-0"><strong>Usa Quantità:</strong> {{ $service->uses_quantity ? 'Sì' : 'No' }}</p>
            </div>
        </div>

        {{-- Date Filter --}}
        <form method="GET" action="{{ route('admin.services.show', $service->id) }}" class="d-flex gap-2 flex-wrap mb-4">
            <div>
                <label for="start_date" class="form-label text-white">Data Inizio</label>
                <input type="date" name="start_date" id="start_date" class="form-control"
                    value="{{ $startDate->toDateString() }}">
            </div>
            <div>
                <label for="end_date" class="form-label text-white">Data Fine</label>
                <input type="date" name="end_date" id="end_date" class="form-control"
                    value="{{ $endDate->toDateString() }}">
            </div>
            <div class="d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">Filtra</button>
                <a href="{{ route('admin.services.show', $service->id) }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <div class="alert alert-success">
            Totale incassato per questo servizio: <strong>€{{ number_format($totalRevenue, 2, ',', '.') }}</strong>
        </div>

        @if (!empty($revenueOverTime) && $revenueOverTime->isNotEmpty())
            <div class="card mb-4">
                <div class="card-body">
                    <canvas id="revenueChart" class="w-100" style="min-height: 300px;"></canvas>

                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($revenueOverTime->keys()) !!},
                        datasets: [{
                            label: 'Incasso Giornaliero (€)',
                            data: {!! json_encode($revenueOverTime->values()) !!},
                            borderWidth: 2,
                            fill: false,
                            tension: 0.1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        @endif

        <h5 class="text-white">Prestazioni con questo servizio</h5>

        <p class="text-muted small mb-2">
            <span class="text-danger fw-bold">*</span> <span class="text-white">I prezzi in rosso con l'asterisco sono
                relativi a prestazioni
                parte di un abbonamento e non vengono calcolati nei totali.</span>
        </p>

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
                                    €{{ number_format($log->custom_price ?? ($log->service->price ?? 0), 2, ',', '.') }}
                                </td>
                                <td>
                                    €{{ number_format($log->custom_price ?? 0, 2, ',', '.') }}
                                    @if ($log->is_part_of_subscription)
                                        <span class="text-danger">*</span>
                                    @endif
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

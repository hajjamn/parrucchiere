@extends('layouts.app')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="container py-4">
        <h1 class="mb-4 text-white">Riepilogo Giornaliero</h1>

        {{-- Filtro per date --}}
        <form method="GET" action="{{ route('admin.summary.index') }}" class="d-flex gap-3 flex-wrap mb-4">
            <div>
                <label for="start_date" class="form-label text-white">Data Inizio</label>
                <input type="date" id="start_date" name="start_date" class="form-control"
                    value="{{ request('start_date', now()->toDateString()) }}">
            </div>

            <div>
                <label for="end_date" class="form-label text-white">Data Fine</label>
                <input type="date" id="end_date" name="end_date" class="form-control"
                    value="{{ request('end_date', now()->toDateString()) }}">
            </div>

            <div class="d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">Filtra</button>
                <a href="{{ route('admin.summary.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        {{-- Totali --}}
        <div class="row text-dark mb-4">
            <div class="col-md-4">
                <div class="card bg-white shadow rounded-3">
                    <div class="card-body">
                        <h5 class="card-title">Totale Incassi</h5>
                        <p class="fs-4">€{{ number_format($totalPrice, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white shadow rounded-3">
                    <div class="card-body">
                        <h5 class="card-title">Totale Percentuali Operatori</h5>
                        <p class="fs-4">€{{ number_format($totalCommission, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white shadow rounded-3">
                    <div class="card-body">
                        <h5 class="card-title">Guadagno Netto</h5>
                        <p class="fs-4">€{{ number_format($netProfit, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>


        {{-- Guadagno per operatore --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Guadagno per Operatore</h5>
                <canvas id="operatorChart"></canvas>
            </div>
        </div>

        {{-- Distribuzione servizi --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Distribuzione Servizi</h5>
                <canvas id="servicesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const operatorChart = new Chart(document.getElementById('operatorChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($userRevenue->keys()) !!},
                datasets: [
                    {
                        label: 'Totale Incassato (€)',
                        data: {!! json_encode($userRevenue->values()) !!},
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Commissione Operatore (€)',
                        data: {!! json_encode($commissionsByUser->values()) !!},
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': €' + context.parsed.y.toFixed(2).replace('.', ',');
                            }
                        }
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '€' + value.toLocaleString('it-IT', { minimumFractionDigits: 2 })
                        }
                    }
                }
            }
        });

        const servicesChart = new Chart(document.getElementById('servicesChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($serviceRevenue->keys()) !!},
                datasets: [{
                    label: 'Incasso per Servizio (€)',
                    data: {!! json_encode($serviceRevenue->values()) !!},
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const countMap = {!! json_encode($servicesCount) !!};
                                const count = countMap[label] || 0;
                                return `${label}: €${value.toFixed(2).replace('.', ',')} (${count} prestazioni)`;
                            }
                        }
                    }
                }
            }
        });


    </script>
@endsection
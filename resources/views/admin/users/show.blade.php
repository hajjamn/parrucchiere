@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 text-white">Profilo Utente</h1>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">{{ $user->first_name }} {{ $user->last_name }}</h5>
                <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
                <p class="card-text"><strong>Ruolo:</strong> {{ ucfirst($user->role) }}</p>
            </div>
        </div>

        {{-- Filtro per date --}}
        <form method="GET" action="{{ route('admin.users.show', $user->id) }}" class="d-flex gap-2 flex-wrap mb-4">
            <div>
                <label for="start_date" class="form-label text-white">Data Inizio</label>
                <input type="date" id="start_date" name="start_date" class="form-control"
                    value="{{ $startDate->toDateString() }}">
            </div>

            <div>
                <label for="end_date" class="form-label text-white">Data Fine</label>
                <input type="date" id="end_date" name="end_date" class="form-control"
                    value="{{ $endDate->toDateString() }}">
            </div>

            <div class="d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">Filtra</button>

                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>

        {{-- Totale guadagnato --}}
        <div class="alert alert-success">
            Totale guadagnato: <strong>€{{ number_format($totalCommission, 2, ',', '.') }}</strong>
        </div>

        <h3 class="mb-3 text-white">Prestazioni Registrate</h3>

        @if ($user->serviceLogs->isEmpty())
            <div class="alert alert-info">Nessuna prestazione registrata per questo utente.</div>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Servizio</th>
                        <th>Data</th>
                        <th>Prezzo</th>
                        <th>Percentuale</th>
                        <th>Commissione</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user->serviceLogs as $log)
                        <tr>
                            <td>{{ $log->client->first_name }} {{ $log->client->last_name }}</td>
                            <td>{{ $log->service->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->performed_at)->format('d/m/Y H:i') }}</td>
                            <td>€{{ number_format($log->custom_price ?? $log->service->price ?? 0, 2, ',', '.') }}</td>
                            <td>{{ $log->service->percentage }}%</td>
                            <td>
                                €{{ number_format((($log->custom_price ?? $log->service->price ?? 0) * $log->service->percentage) / 100, 2, ',', '.') }}
                            </td>

                            @if (auth()->user()->role === 'admin' || auth()->id() === $log->user_id)
                                <td class="text-end">
                                    <a href="{{ route('admin.service-logs.edit', $log->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">Modifica</a>

                                    <form action="{{ route('admin.service-logs.destroy', $log->id) }}" method="POST"
                                        class="d-inline-block"
                                        onsubmit="return confirm('Sei sicuro di voler eliminare questa prestazione?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                    </form>
                                </td>
                            @endif

                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-4">Torna alla lista utenti</a>
    </div>
@endsection
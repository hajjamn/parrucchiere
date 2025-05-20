@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary mt-4 mb-4">Torna alla lista clienti</a>

        <h1 class="mb-4 text-white">Cliente: {{ $client->first_name }} {{ $client->last_name }}</h1>

        <div class="card mb-4">
            <div class="card-body">
                @if(auth()->user()->role === 'admin')
                    <p><strong>Email:</strong> {{ $client->email ?? 'Non disponibile' }}</p>
                    <p><strong>Telefono:</strong> {{ $client->phone }}</p>
                @endif
                <p><strong>Data di nascita:</strong>
                    {{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') : 'Non disponibile' }}
                </p>
            </div>
        </div>

        @if (auth()->user()->role === 'admin')
            <div class="mb-4">
                <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-primary">Modifica</a>

                <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" class="d-inline-block"
                    onsubmit="return confirm('Sei sicuro di voler eliminare questo cliente?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Elimina</button>
                </form>
            </div>
        @endif

        <h3 class="mb-3 text-white">Prestazioni Registrate</h3>

        @if ($client->serviceLogs->isEmpty())
            <div class="alert alert-info">Nessuna prestazione registrata per questo cliente.</div>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Operatore</th>
                        <th>Servizio</th>
                        <th>Data</th>
                        <th>Prezzo</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($client->serviceLogs->sortByDesc('performed_at') as $log)
                        <tr>
                            <td>{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
                            <td>{{ $log->service->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->performed_at)->format('d/m/Y H:i') }}</td>
                            <td>€{{ number_format($log->custom_price ?? $log->service->price ?? 0, 2, ',', '.') }} </td>
                            <td>
                                @if (auth()->user()->role === 'admin' || auth()->id() === $log->user_id)
                                    <a href="{{ route('admin.service-logs.edit', $log->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">Modifica</a>

                                    <form action="{{ route('admin.service-logs.destroy', $log->id) }}" method="POST"
                                        class="d-inline-block"
                                        onsubmit="return confirm('Sei sicuro di voler eliminare questa prestazione?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                    </form>

                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        @endif

    </div>
@endsection
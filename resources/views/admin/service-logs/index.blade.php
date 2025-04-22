@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Prestazioni registrate</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ route('admin.service-logs.create') }}" class="btn btn-primary">
                Aggiungi nuova prestazione
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Operatore</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Servizio</th>
                        <th scope="col">Data</th>
                        @if (auth()->user()->role === 'admin')
                            <th scope="col" class="text-center">Azioni</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
                            <td>{{ $log->client->first_name }} {{ $log->client->last_name }}</td>
                            <td>{{ $log->service->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->performed_at)->format('d/m/Y H:i') }}</td>
                            @if (auth()->user()->role === 'admin')
                                <td class="text-center">
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
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nessuna prestazione trovata.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
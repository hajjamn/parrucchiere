@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Clienti</h1>

        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary mb-3">+ Aggiungi Cliente</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($clients->isEmpty())
            <div class="alert alert-info">Nessun cliente trovato.</div>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Cognome</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th class="text-center">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $client)
                        <tr>
                            <td>{{ $client->last_name }}</td>
                            <td>{{ $client->first_name }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->phone }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-sm btn-outline-secondary me-1">
                                    Dettagli
                                </a>

                                @if (auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.clients.edit', $client) }}"
                                        class="btn btn-sm btn-outline-primary me-1">Modifica</a>

                                    <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Sei sicuro di voler eliminare questo cliente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Elimina</button>
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
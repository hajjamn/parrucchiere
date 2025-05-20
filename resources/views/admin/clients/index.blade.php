@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 text-white">Clienti</h1>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <form method="GET" action="{{ route('admin.clients.index') }}" class="d-flex" role="search">
                <input type="text" name="search" class="form-control me-2" placeholder="Cerca nome o cognome"
                    value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-light">Cerca</button>
            </form>

            <a href="{{ route('admin.clients.create') }}" class="btn btn-success">+ Aggiungi Cliente</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($clients->isEmpty())
            <div class="alert alert-info">Nessun cliente trovato.</div>
        @else
            <div class="table-responsive">
                <table
                    class="table table-bordered align-middle text-center w-100 {{ auth()->user()->role === 'admin' ? 'table-admin' : '' }}">
                    <thead>
                        <tr>
                            <th class="align-middle">Cognome</th>
                            <th class="align-middle">Nome</th>
                            @if(auth()->user()->role === 'admin')
                                <th class="align-middle">Email</th>
                                <th class="align-middle">Telefono</th>
                            @endif
                            <th class="align-middle">Data di nascita</th>
                            <th class="text-center align-middle">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $client)
                            <tr>
                                <td class="align-middle">{{ $client->last_name }}</td>
                                <td class="align-middle">{{ $client->first_name }}</td>
                                @if(auth()->user()->role === 'admin')
                                    <td class="align-middle" title="{{ $client->email }}">{{ $client->email }}</td>
                                    <td class="align-middle" title="{{ $client->phone }}">{{ $client->phone }}</td>
                                @endif
                                <td class="align-middle">
                                    {{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') : '—' }}
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <a href="{{ route('admin.clients.show', $client) }}"
                                            class="btn btn-sm btn-outline-secondary">Dettagli</a>

                                        @if (auth()->user()->role === 'admin')
                                            <a href="{{ route('admin.clients.edit', $client) }}"
                                                class="btn btn-sm btn-outline-primary">Modifica</a>

                                            <form action="{{ route('admin.clients.destroy', $client) }}" method="POST"
                                                onsubmit="return confirm('Sei sicuro di voler eliminare questo cliente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Elimina</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
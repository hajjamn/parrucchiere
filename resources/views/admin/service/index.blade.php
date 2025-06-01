@extends('layouts.app')

@section('content')

    <div class="container py-4">
        <h1 class="mb-4 d-flex justify-content-between align-items-center">
            <span class="text-white">Servizi</span>
            <a href="{{ route('admin.services.create') }}" class="btn btn-success btn-sm">+ Nuovo Servizio</a>
        </h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($services->isEmpty())
            <div class="alert alert-info">Nessun servizio disponibile.</div>
        @else
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Prezzo</th>
                        <th>Percentuale</th>
                        <th>Prezzo Variabile</th>
                        <th>Usa Quantità</th>
                        <th class="text-center">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                        <tr>
                            <td>{{ $service->name }}</td>
                            <td>
                                @if ($service->price)
                                    €{{ number_format($service->price, 2, ',', '.') }}
                                @else
                                    <em>Da definire</em>
                                @endif
                            </td>
                            <td>{{ $service->percentage }}%</td>
                            <td>{{ $service->is_variable_price ? 'Sì' : 'No' }}</td>
                            <td>{{ $service->uses_quantity ? 'Sì' : 'No' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.services.edit', $service) }}"
                                    class="btn btn-sm btn-outline-primary me-1">Modifica</a>
                                <a href="{{ route('admin.services.show', $service) }}"
                                    class="btn btn-sm btn-outline-secondary me-1">Visualizza</a>
                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline-block"
                                    onsubmit="return confirm('Sei sicuro di voler eliminare questo servizio?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
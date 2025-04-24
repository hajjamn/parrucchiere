@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Storico Prestazioni</h1>

        <div class="mb-3">
            <a href="{{ route('admin.service-logs.create') }}" class="btn btn-primary">
                Aggiungi Prestazione
            </a>
        </div>

        {{-- 🎉 Compleanni Oggi --}}
        @php
            $today = \Carbon\Carbon::today();
            $birthdayClients = \App\Models\Client::whereMonth('birth_date', $today->month)
                ->whereDay('birth_date', $today->day)
                ->get();
        @endphp

        @if ($birthdayClients->isNotEmpty())
            <div class="alert alert-info">
                <h5 class="mb-2">🎉 Compleanni di oggi:</h5>
                <ul class="mb-0">
                    @foreach ($birthdayClients as $client)
                        <li>
                            <a href="{{ route('admin.clients.show', $client->id) }}">
                                {{ $client->first_name }} {{ $client->last_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Storico prestazioni --}}
        @forelse ($logs as $date => $clients)
            <h3 class="mt-4 mb-3">{{ ucwords(\Carbon\Carbon::parse($date)->translatedFormat('l d F Y')) }}</h3>

            @foreach ($clients as $clientId => $serviceLogs)
                <div class="card mb-4 shadow">
                    <div class="card-header">
                        <strong>{{ $serviceLogs->first()->client->first_name }}
                            {{ $serviceLogs->first()->client->last_name }}</strong>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    @if (auth()->user()->role === 'admin')
                                        <th>Operatore</th>
                                    @endif
                                    <th>Servizio</th>
                                    <th>Data</th>
                                    <th>Prezzo</th>
                                    <th>Percentuale</th>
                                    <th>Totale</th>
                                    <th class="text-center">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($serviceLogs as $log)
                                    <tr>
                                        @if (auth()->user()->role === 'admin')
                                            <td>{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
                                        @endif
                                        <td>{{ $log->service->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($log->performed_at)->format('H:i') }}</td>
                                        <td>€{{ number_format($log->service->price ?? 0, 2, ',', '.') }}</td>
                                        <td>{{ $log->service->percentage }}%</td>
                                        <td>€{{ number_format(($log->service->price ?? 0) * $log->service->percentage / 100, 2, ',', '.') }}</td>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="alert alert-info">Nessuna prestazione trovata.</div>
        @endforelse
    </div>
@endsection

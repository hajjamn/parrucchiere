@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 text-white">Storico Prestazioni</h1>

        <div class="mb-3">
            <a href="{{ route('admin.service-logs.create') }}" class="btn btn-primary">
                + Aggiungi Prestazione
            </a>
        </div>

        @php
            use Illuminate\Support\Carbon;

            $today = Carbon::today();
            $startOfWeek = $today->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = $today->copy()->endOfWeek(Carbon::SUNDAY);

            // Preparo i compleanni mappando la birth_date nell'anno corrente
            $clients = \App\Models\Client::all();

            // Compleanni di oggi
            $birthdayClientsToday = $clients->filter(function ($client) use ($today) {
                return $client->birth_date &&
                    Carbon::createFromFormat('Y-m-d', $today->year . '-' . date('m-d', strtotime($client->birth_date)))
                        ->isSameDay($today);
            });

            // Compleanni di questa settimana (escludendo oggi)
            $birthdayClientsWeek = $clients->filter(function ($client) use ($startOfWeek, $endOfWeek, $today) {
                if (!$client->birth_date)
                    return false;

                $birthdayThisYear = Carbon::createFromFormat('Y-m-d', $today->year . '-' . date('m-d', strtotime($client->birth_date)));

                return $birthdayThisYear->isBetween($startOfWeek, $endOfWeek) && !$birthdayThisYear->isSameDay($today);
            });
        @endphp

        @if ($birthdayClientsToday->isNotEmpty())
            <div class="alert alert-info">
                <h5 class="mb-2">🎉 Compleanni di oggi:</h5>
                <ul class="mb-0">
                    @foreach ($birthdayClientsToday as $client)
                        <li>
                            <a href="{{ route('admin.clients.show', $client->id) }}">
                                {{ $client->first_name }} {{ $client->last_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($birthdayClientsWeek->isNotEmpty())
            <div class="alert alert-warning">
                <h5 class="mb-2">📅 Compleanni di questa settimana:</h5>
                <ul class="mb-0">
                    @foreach ($birthdayClientsWeek as $client)
                            @php
                                $birthdayThisYear = Carbon::createFromFormat('Y-m-d', now()->year . '-' . date('m-d', strtotime($client->birth_date)));
                            @endphp
                            <li>
                                <a href="{{ route('admin.clients.show', $client->id) }}">
                                    {{ $client->first_name }} {{ $client->last_name }}
                                </a>
                                — {{ ucwords($birthdayThisYear->translatedFormat('l d F')) }}
                            </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Storico prestazioni --}}
        @forelse ($logs as $date => $clients)
            <h3 class="mt-4 mb-3 text-white">
                {{ ucwords(\Carbon\Carbon::createFromFormat('Y-m-d', $date)->translatedFormat('l d F Y')) }}
            </h3>

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
                                        <td>€{{ number_format($log->custom_price ?? $log->service->price ?? 0, 2, ',', '.') }}</td>
                                        <td>{{ $log->service->percentage }}%</td>
                                        <td>€{{ number_format(($log->custom_price ?? $log->service->price ?? 0) * $log->service->percentage / 100, 2, ',', '.') }}
                                        </td>
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
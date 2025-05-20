@extends('layouts.app')

@section('content')
    <div class="container py-4">
        @php
            use Illuminate\Support\Carbon;

            $today = Carbon::today();
            $startOfWeek = $today->copy()->startOfWeek(weekStartsAt: Carbon::MONDAY);
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
            $birthdayClientsWeek = $clients
        ->filter(function ($client) use ($startOfWeek, $endOfWeek, $today) {
            if (!$client->birth_date) return false;

            $birthdayThisYear = Carbon::createFromFormat('Y-m-d', $today->year . '-' . date('m-d', strtotime($client->birth_date)));

            return $birthdayThisYear->isBetween($startOfWeek, $endOfWeek) && !$birthdayThisYear->isSameDay($today);
        })
        ->sortBy(function ($client) use ($today) {
            return Carbon::createFromFormat('Y-m-d', $today->year . '-' . date('m-d', strtotime($client->birth_date)));
        })
        ->groupBy(function ($client) use ($today) {
            return Carbon::createFromFormat('Y-m-d', $today->year . '-' . date('m-d', strtotime($client->birth_date)))
                ->translatedFormat('l'); // Day name like 'Lunedì'
        });
        @endphp

        @if ($abbonamentoZeroLogs->isNotEmpty())
            <div class="alert alert-danger">
                <strong>⚠️ Attenzione:</strong> Ci sono delle prestazioni con <strong>Abbonamento</strong> a prezzo 0.
                <ul class="mb-0 mt-2">
                    @foreach ($abbonamentoZeroLogs as $log)
                        <li>
                            <a href="{{ route('admin.service-logs.edit', $log->id) }}" class="text-white text-decoration-underline">
                                {{ $log->performed_at->format('d/m/Y H:i') }} – {{ $log->client->first_name }}
                                {{ $log->client->last_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif


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
            @foreach ($birthdayClientsWeek as $day => $clientsForDay)
                <li class="fw-bold">{{ ucfirst($day) }}:</li>
                <ul class="mb-2">
                    @foreach ($clientsForDay as $client)
                        <li>
                            <a href="{{ route('admin.clients.show', $client->id) }}">
                                {{ $client->first_name }} {{ $client->last_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </ul>
    </div>
@endif

        <h1 class="mb-4 text-white">Storico Prestazioni</h1>

        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
                <a href="{{ route('admin.service-logs.index', ['date' => $selectedDate->copy()->subDay()->toDateString()]) }}"
                    class="btn btn-primary btn-sm">
                    ← Giorno precedente
                </a>

                <form method="GET" action="{{ route('admin.service-logs.index') }}">
                    <input type="date" name="date" class="form-control" value="{{ $selectedDate->toDateString() }}"
                        onchange="this.form.submit()">
                </form>

                <a href="{{ route('admin.service-logs.index', ['date' => $selectedDate->copy()->addDay()->toDateString()]) }}"
                    class="btn btn-primary btn-sm">
                    Giorno successivo →
                </a>
            </div>

            <div>
                <a href="{{ route('admin.service-logs.create') }}" class="btn btn-success">
                    + Aggiungi Prestazione
                </a>
            </div>
        </div>

        {{-- Storico prestazioni --}}
        @forelse ($logs as $date => $clients)
            <h3 class="mt-4 mb-3 text-white">
                {{ ucwords(\Carbon\Carbon::createFromFormat('Y-m-d', $date)->translatedFormat('l d F Y')) }}
            </h3>

            @foreach ($clients as $clientId => $serviceLogs)
                <div class="card mb-4 shadow bg-secondary text-white">
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

                                @php
    $isAbbonamentoZero = strtolower($log->service->name) === 'abbonamento' && ($log->custom_price ?? 0) == 0;
@endphp

<tr @if(auth()->user()->role === 'admin' && $isAbbonamentoZero) class="table-danger" @endif>
                                        @if (auth()->user()->role === 'admin')
                                            <td class="align-middle">{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
                                        @endif
                                        <td class="align-middle">{{ $log->service->name }}</td>
                                        <td class="align-middle">{{ \Carbon\Carbon::parse($log->performed_at)->format('H:i') }}</td>
                                        <td class="align-middle">
                                            €{{ number_format($log->custom_price ?? $log->service->price ?? 0, 2, ',', '.') }}</td>
                                        <td class="align-middle">{{ $log->service->percentage }}%</td>
                                        <td class="align-middle">
                                            €{{ number_format(($log->custom_price ?? $log->service->price ?? 0) * $log->service->percentage / 100, 2, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                <a href="{{ route('admin.service-logs.edit', $log->id) }}"
                                                    class="btn btn-sm btn-outline-primary">Modifica</a>

                                                <form action="{{ route('admin.service-logs.destroy', $log->id) }}" method="POST"
                                                    onsubmit="return confirm('Sei sicuro di voler eliminare questa prestazione?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                                </form>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- TOTALI --}}
                        <div class="card-footer bg-dark text-white">
                            @php
                                $totalPrice = $serviceLogs->sum(function ($log) {
                                    return $log->custom_price ?? $log->service->price ?? 0;
                                });

                                $totalPercentageValue = $serviceLogs->sum(function ($log) {
                                    $price = $log->custom_price ?? $log->service->price ?? 0;
                                    return $price * ($log->service->percentage ?? 0) / 100;
                                });
                            @endphp

                            <div class="d-flex justify-content-between">
                                <span><strong>Totale Prezzo:</strong> €{{ number_format($totalPrice, 2, ',', '.') }}</span>
                                <span><strong>Totale Percentuale:</strong>
                                    €{{ number_format($totalPercentageValue, 2, ',', '.') }}</span>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        @empty
            <div class="alert alert-info">Nessuna prestazione trovata.</div>
        @endforelse
    </div>
@endsection
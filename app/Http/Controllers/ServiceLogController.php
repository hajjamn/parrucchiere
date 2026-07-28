<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceLogUpdateRequest;
use App\Http\Requests\ServiceLogStoreRequest;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use App\Models\ServiceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ServiceLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Data selezionata (o oggi di default)
        $selectedDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        $query = ServiceLog::with(['client', 'service', 'user'])
            ->whereDate('performed_at', $selectedDate)
            ->orderByDesc('performed_at');

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $logs = $query->get()
            ->groupBy(function ($log) {
                return Carbon::parse($log->performed_at)->format('Y-m-d');
            })
            ->map(function ($logsForDate) {
                return $logsForDate->groupBy('client_id');
            })
            ->sortKeysDesc();

        $abbonamentoZeroLogs = collect();

        if ($user->role === 'admin') {
            $abbonamentoZeroLogs = ServiceLog::with(['client', 'service', 'user'])
                ->whereDate('performed_at', $selectedDate)
                ->whereHas('service', function ($query) {
                    $query->whereRaw('LOWER(name) = ?', ['abbonamento']);
                })
                ->where(function ($query) {
                    $query->whereNull('custom_price')->orWhere('custom_price', 0);
                })
                ->get();
        }

        // 🎂 Compleanni (da usare nella view)
        $clients = Client::all();
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $today->copy()->endOfWeek(Carbon::SUNDAY);

        $birthdayClientsToday = $clients->filter(function ($client) use ($today) {
            return $client->birth_date &&
                Carbon::createFromFormat('Y-m-d', $today->year . '-' . date('m-d', strtotime($client->birth_date)))
                    ->isSameDay($today);
        });

        $birthdayClientsWeek = $clients
            ->filter(function ($client) use ($startOfWeek, $endOfWeek, $today) {
                if (!$client->birth_date)
                    return false;

                $birthdayThisYear = Carbon::createFromFormat('Y-m-d', $today->year . '-' . date('m-d', strtotime($client->birth_date)));

                return $birthdayThisYear->isBetween($startOfWeek, $endOfWeek) && !$birthdayThisYear->isSameDay($today);
            })
            ->sortBy(function ($client) use ($today) {
                return Carbon::createFromFormat('Y-m-d', $today->year . '-' . date('m-d', strtotime($client->birth_date)));
            });

        return view('admin.service-logs.index', compact(
            'logs',
            'selectedDate',
            'abbonamentoZeroLogs',
            'birthdayClientsToday',
            'birthdayClientsWeek'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::orderBy('last_name')->orderBy('first_name')->get();

        $services = Service::orderBy('name')->get();

        return view('admin.service-logs.create', compact('clients', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceLogStoreRequest $request)
    {
        $services = $request->input('services', []);
        $isAdmin = auth()->user()->role === 'admin';
        $isSubscription = $request->has('is_part_of_subscription');

        foreach ($services as $item) {
            if (empty($item['id']))
                continue;

            $service = Service::find($item['id']);
            if (!$service)
                continue;

            // Initialize
            $customPrice = null;
            $quantity = null;

            // Abbonamento logic
            if (strtolower($service->name) === 'abbonamento') {
                $customPrice = $isAdmin ? ($item['entry'] ?? 0) : 0;
            }

            // Quantity logic (e.g., Extensions)
            elseif ($service->uses_quantity && isset($item['entry'])) {
                $quantity = (int) $item['entry'];
                $customPrice = $quantity * $service->price;
            }

            // Variable price logic
            elseif ($service->is_variable_price && isset($item['entry'])) {
                $customPrice = $item['entry'];
            }

            // Fixed price fallback (no entry expected)
            elseif (!$service->is_variable_price && !$service->uses_quantity) {
                $customPrice = $service->price;
            }

            if ($isSubscription && $customPrice !== null) {
                $customPrice = round($customPrice * 0.9, 2); // apply 10% discount
            }

            $commissionPercentage = $service->percentage ?? 0;
            $customCommission = ($customPrice * $commissionPercentage) / 100;

            // Save to DB
            ServiceLog::create([
                'user_id' => auth()->id(),
                'client_id' => $request->client_id,
                'service_id' => $service->id,
                'performed_at' => $request->performed_at,
                'custom_price' => $customPrice,
                'commission_percentage' => $commissionPercentage,
                'quantity' => $quantity,
                'custom_commission' => $customCommission,
                'is_part_of_subscription' => $isSubscription
            ]);
        }

        return redirect()->route('admin.service-logs.index')->with('success', 'Prestazione registrata con successo.');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceLog $serviceLog)
    {
        $this->authorizeUserOrAdmin($serviceLog);

        $clients = Client::all();
        $services = Service::all();

        return view('admin.service-logs.edit', compact('serviceLog', 'clients', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceLogUpdateRequest $request, ServiceLog $serviceLog)
    {
        $this->authorizeUserOrAdmin($serviceLog);
        $data = $request->validated();
        $data['is_part_of_subscription'] = $request->boolean('is_part_of_subscription');


        $isAdmin = auth()->user()->role === 'admin';
        $service = Service::find($data['service_id']);

        if (!$service) {
            return back()->withErrors(['service_id' => 'Servizio non trovato.']);
        }

        // Handle Abbonamento restriction
        if (strtolower($service->name) === 'abbonamento' && !$isAdmin) {
            unset($data['custom_price']);
        }

        // Handle quantity-based pricing
        if (isset($data['quantity']) && is_numeric($data['quantity'])) {
            $data['custom_price'] = $service->price * (int) $data['quantity'];
        } elseif (!isset($data['custom_price'])) {
            // Neither quantity nor custom_price — clear both
            $data['custom_price'] = null;
            $data['quantity'] = null;
        }

        /* QUESTO DA AGGIUNGERE SOLO SE VOGLIONO RICALCOLARE COMMISSIONI DOPO SCONTI */
        $commissionPercentage = $serviceLog->commission_percentage ?? 0;
        $data['custom_commission'] = ($data['custom_price'] ?? 0) * $commissionPercentage / 100;

        $serviceLog->update($data);

        return redirect()->route('admin.service-logs.index')->with('success', 'Prestazione aggiornata con successo.');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceLog $serviceLog)
    {
        $this->authorizeUserOrAdmin($serviceLog);

        $serviceLog->delete();

        return redirect()->route('admin.service-logs.index')->with('success', 'Prestazione eliminata con successo.');
    }

    private function authorizeUserOrAdmin(ServiceLog $serviceLog)
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $serviceLog->user_id !== $user->id) {
            abort(403, 'Non autorizzato.');
        }
    }
}

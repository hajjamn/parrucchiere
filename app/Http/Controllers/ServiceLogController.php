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
                return \Carbon\Carbon::parse($log->performed_at)->format('Y-m-d');
            })
            ->map(function ($logsForDate) {
                return $logsForDate->groupBy('client_id');
            })
            ->sortKeysDesc();

        return view('admin.service-logs.index', compact('logs', 'selectedDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::orderBy('last_name')->orderBy('first_name')->get();

        $services = Service::all();

        return view('admin.service-logs.create', compact('clients', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceLogStoreRequest $request)
    {
        $customPrices = $request->input('custom_prices', []);
        $isAdmin = auth()->user()->role === 'admin';

        foreach ($request->service_ids as $serviceId) {
            $service = Service::find($serviceId);

            $customPrice = $customPrices[$serviceId] ?? null;

            // Block custom price input for "Abbonamento" if not admin
            if (!$isAdmin && strtolower($service->name) === 'abbonamento') {
                $customPrice = 0;
            }

            ServiceLog::create([
                'user_id' => Auth::id(),
                'client_id' => $request->client_id,
                'service_id' => $serviceId,
                'performed_at' => $request->performed_at,
                'custom_price' => $customPrice,
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

        // Prevent custom_price update if user is NOT admin and service is "Abbonamento"
        $isAdmin = auth()->user()->role === 'admin';
        $service = Service::find($data['service_id']);

        if (!$isAdmin && strtolower($service?->name ?? '') === 'abbonamento') {
            unset($data['custom_price']); // Prevent overwrite
        }

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

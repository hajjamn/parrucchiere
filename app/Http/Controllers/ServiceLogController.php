<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceLogUpdateRequest;
use App\Http\Requests\ServiceLogStoreRequest;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use App\Models\ServiceLog;
use Illuminate\Support\Facades\Auth;

class ServiceLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $logs = $user->role == 'admin'
            ? ServiceLog::with(['user', 'client', 'service'])->latest()->get()
            : $user->serviceLogs()->with(['client', 'service'])->latest()->get();

        return view('admin.service-logs.index', compact('logs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all();
        $services = Service::all();

        return view('admin.service-logs.create', compact('clients', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceLogStoreRequest $request)
    {
        foreach ($request->service_ids as $serviceId) {
            ServiceLog::create([
                'user_id' => Auth::id(),
                'client_id' => $request->client_id,
                'service_id' => $serviceId,
                'performed_at' => $request->performed_at
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
        $this->authorizeAdmin();

        $clients = Client::all();
        $services = Service::all();

        return view('admin.service-logs.edit', compact('serviceLog', 'clients', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceLogUpdateRequest $request, ServiceLog $serviceLog)
    {
        $this->authorizeAdmin();

        $serviceLog->update([
            'client_id' => $request->client_id,
            'service_id' => $request->service_id,
            'performed_at' => $request->performed_at,
        ]);

        return redirect()->route('admin.service-logs.index')->with('success', 'Prestazione aggiornata con successo.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceLog $serviceLog)
    {
        $this->authorizeAdmin();

        $serviceLog->delete();

        return redirect()->route('admin.service-logs.index')->with('success', 'Prestazione eliminata con successo.');
    }

    private function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Non autorizzato.');
        }
    }
}

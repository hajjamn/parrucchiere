<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('admin.service.index', compact('services'));
    }

    public function show(Service $service)
    {
        $this->authorizeAdmin();

        $startDate = request('start_date') ? \Carbon\Carbon::parse(request('start_date')) : now()->startOfMonth();
        $endDate = request('end_date') ? \Carbon\Carbon::parse(request('end_date')) : now()->endOfMonth();

        $service->load([
            'serviceLogs' => function ($query) use ($startDate, $endDate) {
                $query->with(['client', 'user'])
                    ->whereBetween('performed_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                    ->orderByDesc('performed_at');
            }
        ]);

        $totalRevenue = $service->serviceLogs->sum(function ($log) {
            return $log->custom_price ?? $log->service->price ?? 0;
        });

        $revenueOverTime = $service->serviceLogs
            ->groupBy(fn($log) => \Carbon\Carbon::parse($log->performed_at)->format('Y-m-d'))
            ->map(fn($logs) => $logs->sum(function ($log) {
                return $log->custom_price ?? $log->service->price ?? 0;
            }));

        return view('admin.service.show', compact('service', 'startDate', 'endDate', 'totalRevenue', 'revenueOverTime'));
    }



    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.service.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'percentage' => 'required|numeric|min:0|max:100'
        ]);

        $isVariablePrice = $request->boolean('is_variable_price');
        $usesQuantity = $request->boolean('uses_quantity');

        if ($isVariablePrice && $usesQuantity) {
            return redirect()->back()
                ->withErrors(['is_variable_price' => 'Un servizio non può avere sia prezzo variabile che quantità.'])
                ->withInput();
        }

        $validated['is_variable_price'] = $isVariablePrice;
        $validated['uses_quantity'] = $usesQuantity;

        // Force null price if variable
        if ($isVariablePrice) {
            $validated['price'] = null;
        }

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Servizio creato con successo.');
    }

    public function edit(Service $service)
    {
        $this->authorizeAdmin();
        return view('admin.service.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $isVariablePrice = $request->boolean('is_variable_price');
        $usesQuantity = $request->boolean('uses_quantity');

        if ($isVariablePrice && $usesQuantity) {
            return redirect()->back()
                ->withErrors(['is_variable_price' => 'Un servizio non può avere sia prezzo variabile che quantità.'])
                ->withInput();
        }

        $validated['is_variable_price'] = $isVariablePrice;
        $validated['uses_quantity'] = $usesQuantity;

        // Force null price if variable
        if ($isVariablePrice) {
            $validated['price'] = null;
        }

        // Prevent updating price if the service is "Abbonamento"
        if (strtolower($service->name) === 'abbonamento') {
            unset($validated['price']); // Strip price if someone tries to submit it anyway
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Servizio modificato con successo.');
    }


    public function destroy(Service $service)
    {
        $this->authorizeAdmin();

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Servizio eliminato con successo');
    }

    private function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Non autorizzato.');
        }
    }
}

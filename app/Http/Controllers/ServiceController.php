<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'percentage' => 'required|numeric|min:0|max:100'
        ]);

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Servizio creato con successo.');
    }

    public function edit(Service $service)
    {
        $this->authorizeAdmin();
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

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

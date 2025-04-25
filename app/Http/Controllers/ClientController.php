<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Client::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('last_name')->get();

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientStoreRequest $request)
    {
        $client = Client::create($request->validated());

        // If the form was submitted from the modal (ServiceLog create)
        if ($request->input('_from_modal')) {
            return redirect()->back()
                ->with('new_client_id', $client->id)
                ->with('success', 'Cliente creato con successo.');
        }

        // Otherwise, redirect to clients index
        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente creato con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {

        $client->load([
            'serviceLogs' => function ($query) {
                $query->orderByDesc('performed_at');
            },
            'serviceLogs.user',
            'serviceLogs.service'
        ]);


        return view('admin.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientUpdateRequest $request, Client $client)
    {
        $this->authorizeAdmin();

        $client->update($request->validated());

        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $this->authorizeAdmin();

        $client->delete();

        return redirect()->route('admin.clients.index')->with('success', 'Cliente eliminato con successo');
    }

    protected function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
    }
}

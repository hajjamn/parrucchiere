<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of all users (admin only).
     */
    public function index()
    {
        $this->authorizeAdmin();

        $users = User::all();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user's profile and service logs.
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id !== $user->id && $currentUser->role !== 'admin') {
            abort(403, 'Non sei autorizzato a vedere questo profilo.');
        }

        $user->load([
            'serviceLogs' => function ($query) {
                $query->with(['client', 'service'])->orderByDesc('performed_at');
            }
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validated();

        User::create([
            ...$validated,
            'role' => 'user',
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utente creato con successo.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Non puoi eliminare il tuo stesso account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utente eliminato con successo.');
    }

    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accesso riservato agli amministratori.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Prevent unauthorized access.
     */
    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accesso riservato agli amministratori.');
        }
    }
}

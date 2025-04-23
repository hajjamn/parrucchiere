<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SwitchUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('first_name')->get();
        return view('admin.switch-user.index', compact('users'));
    }

    public function switch(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'password' => ['required'],
        ]);

        $user = User::findOrFail($request->user_id);

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['password' => 'Password errata.'])->withInput();
        }

        Auth::login($user);

        return redirect()->route('admin.service-logs.index')->with('status', 'Utente cambiato con successo.');
    }
}


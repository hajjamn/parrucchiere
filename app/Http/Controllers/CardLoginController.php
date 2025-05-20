<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CardLoginController extends Controller
{
    public function show(User $user)
    {
        return view('auth.card-login', compact('user'));
    }

    public function login(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password errata.'])->withInput();
        }

        Auth::login($user);

        return redirect()->route('admin.service-logs.index')->with('status', 'Accesso effettuato come ' . $user->first_name);
    }
}

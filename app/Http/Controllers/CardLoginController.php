<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CardLoginController extends Controller
{
    public function show($slug)
    {
        $user = User::whereRaw('LOWER(SUBSTRING_INDEX(email, "@", 1)) = ?', [strtolower($slug)])->firstOrFail();
        return view('auth.card-login', compact('user'));
    }

    public function login(Request $request, $slug)
    {
        $user = User::whereRaw('LOWER(SUBSTRING_INDEX(email, "@", 1)) = ?', [strtolower($slug)])->firstOrFail();

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

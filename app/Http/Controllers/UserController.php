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

        $startDate = request('start_date') ? \Carbon\Carbon::parse(request('start_date')) : now()->startOfMonth();
        $endDate = request('end_date') ? \Carbon\Carbon::parse(request('end_date')) : now()->endOfMonth();

        $user->load([
            'serviceLogs' => function ($query) use ($startDate, $endDate) {
                $query->with(['client', 'service'])
                    ->whereBetween('performed_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                    ->orderByDesc('performed_at');
            }
        ]);

        $totalCommission = $user->serviceLogs->sum(function ($log) {
            $price = $log->custom_price ?? $log->service->price ?? 0;
            return ($price * $log->service->percentage) / 100;
        });

        // Group commission by day
        $commissionOverTime = $user->serviceLogs
            ->groupBy(fn($log) => \Carbon\Carbon::parse($log->performed_at)->format('Y-m-d'))
            ->map(fn($logs) => $logs->sum(function ($log) {
                $price = $log->custom_price ?? $log->service->price ?? 0;
                return ($price * $log->service->percentage) / 100;
            }));

        // Count of services by service name
        $servicesCount = $user->serviceLogs
            ->groupBy('service.name')
            ->map(fn($logs) => $logs->count());

        $commissionByService = $user->serviceLogs
            ->groupBy('service.name')
            ->map(function ($logs) {
                return $logs->sum(function ($log) {
                    $price = $log->custom_price ?? $log->service->price ?? 0;
                    return ($price * $log->service->percentage) / 100;
                });
            });



        return view('admin.users.show', compact(
            'user',
            'startDate',
            'endDate',
            'totalCommission',
            'commissionOverTime',
            'servicesCount',
            'commissionByService'
        ));

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

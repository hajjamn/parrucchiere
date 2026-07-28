<?php

namespace App\Http\Controllers;

use App\Models\ServiceLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SummaryController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $start = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->startOfDay();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        $logs = ServiceLog::with(['user', 'service'])
            ->whereBetween('performed_at', [$start, $end])
            ->get();

        // Total revenue
        $totalPrice = $logs->reject(fn($log) => $log->is_part_of_subscription)->sum('custom_price');

        // Total commission
        $totalCommission = $logs->sum(fn($log) => $log->custom_commission);

        // Net profit
        $netProfit = $totalPrice - $totalCommission;

        // Commission per user
        $commissionsByUser = $logs
            ->groupBy('user_id')
            ->mapWithKeys(function ($group, $userId) {
                $user = optional($group->first())->user;
                $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                $name = $name !== '' ? $name : "Operatore #{$userId} (sconosciuto)";
                $commission = $group->sum(fn($log) => (float) $log->custom_commission);
                return [$name => $commission];
            });

        // Count of services provided (grouped by service name)
        $servicesCount = $logs
            ->groupBy(fn($log) => $log->service->name ?? 'Senza nome')
            ->map->count();

        // Revenue per service
        $serviceRevenue = $logs
            ->reject(fn($log) => (bool) $log->is_part_of_subscription)
            ->groupBy(fn($log) => $log->service->name ?? 'Senza nome')
            ->map(fn($group) => $group->sum(fn($log) => (float) $log->custom_price));

        // Revenue per user
        $userRevenue = $logs
            ->reject(fn($log) => (bool) $log->is_part_of_subscription)
            ->groupBy('user_id')
            ->mapWithKeys(function ($group, $userId) {
                $user = optional($group->first())->user;
                $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                $name = $name !== '' ? $name : "Operatore #{$userId} (sconosciuto)";
                $total = $group->sum(fn($log) => (float) $log->custom_price);
                return [$name => $total];
            });

        return view('admin.summary.index', compact(
            'totalPrice',
            'totalCommission',
            'netProfit',
            'commissionsByUser',
            'servicesCount',
            'serviceRevenue',
            'userRevenue',
            'start',
            'end'
        ));
    }
}

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

        $start = $request->input('start_date') ? Carbon::parse($request->start_date) : now()->startOfDay();
        $end = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $logs = ServiceLog::with('user')
            ->whereBetween('performed_at', [$start, $end])
            ->get();

        // Total revenue
        $totalPrice = $logs->reject(fn($log) => $log->is_part_of_subscription)->sum('custom_price');

        // Total commission
        $totalCommission = $logs->sum(fn($log) => $log->custom_commission);

        // Net profit
        $netProfit = $totalPrice - $totalCommission;

        // Commission per user
        $commissionsByUser = $logs->groupBy('user_id')->mapWithKeys(function ($logs, $userId) {
            $user = User::find($userId);
            $commission = $logs->sum(fn($log) => $log->custom_commission);
            return [$user->first_name . ' ' . $user->last_name => $commission];
        });

        // Count of services provided (grouped by service name)
        $servicesCount = $logs->groupBy('service.name')->map->count();

        // Revenue per service
        $serviceRevenue = $logs
            ->reject(fn($log) => $log->is_part_of_subscription)
            ->groupBy('service.name')
            ->map(fn($logs) => $logs->sum('custom_price'));

        // Revenue per user
        $userRevenue = $logs
            ->reject(fn($log) => $log->is_part_of_subscription)
            ->groupBy('user_id')
            ->mapWithKeys(function ($logs, $userId) {
                $user = User::find($userId);
                $total = $logs->sum('custom_price');
                return [$user->first_name . ' ' . $user->last_name => $total];
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

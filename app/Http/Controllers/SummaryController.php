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

        $logs = ServiceLog::with(['user', 'service'])
            ->whereBetween('performed_at', [$start, $end])
            ->get();

        $totalPrice = $logs->sum(fn($log) => $log->custom_price ?? $log->service->price);
        $totalCommission = $logs->sum(fn($log) => ($log->custom_price ?? $log->service->price) * ($log->service->percentage / 100));
        $netProfit = $totalPrice - $totalCommission;

        $commissionsByUser = $logs->groupBy('user_id')->map(function ($logs, $userId) {
            return $logs->sum(function ($log) {
                return ($log->custom_price ?? $log->service->price) * ($log->service->percentage / 100);
            });
        });

        $commissionsByUser = $commissionsByUser->mapWithKeys(function ($value, $userId) {
            $user = User::find($userId);
            return [$user->first_name . ' ' . $user->last_name => $value];
        });

        $servicesCount = $logs->groupBy('service.name')->map->count();

        $serviceRevenue = $logs->groupBy('service.name')->map(function ($logs) {
            return $logs->sum(function ($log) {
                return $log->custom_price ?? $log->service->price;
            });
        });


        $userRevenue = $logs->groupBy('user_id')->mapWithKeys(function ($logs, $userId) {
            $user = \App\Models\User::find($userId);
            $total = $logs->sum(function ($log) {
                return $log->custom_price ?? $log->service->price ?? 0;
            });
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


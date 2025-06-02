<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ServiceLog;
use App\Models\Service;

return new class extends Migration {
    public function up(): void
    {
        // Fix 1: Set uses_quantity to true for "extensions"
        Service::whereRaw('LOWER(name) = ?', ['extensions'])->update([
            'uses_quantity' => true,
        ]);

        // Fix 2: Backfill commission_percentage and custom_price
        ServiceLog::with('service')
            ->chunk(100, function ($logs) {
                foreach ($logs as $log) {
                    $service = $log->service;

                    if (!$service) {
                        continue;
                    }

                    // Only update if values are missing
                    if (is_null($log->custom_price)) {
                        $log->custom_price = $service->price ?? 0;
                    }

                    if (is_null($log->commission_percentage)) {
                        $log->commission_percentage = $service->percentage ?? 0;
                    }

                    $log->save();
                }
            });
    }

    public function down(): void
    {
        // Optional: reverse changes if needed
        Service::whereRaw('LOWER(name) = ?', ['extensions'])->update([
            'uses_quantity' => false,
        ]);

        // Reset fields to null (dangerous in production — only do this if needed for rollbacks)
        ServiceLog::query()->update([
            'custom_price' => null,
            'commission_percentage' => null,
        ]);
    }
};

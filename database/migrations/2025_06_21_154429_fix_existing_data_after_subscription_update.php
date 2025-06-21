<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ServiceLog;

return new class extends Migration {
    public function up(): void
    {
        ServiceLog::chunk(100, function ($logs) {
            foreach ($logs as $log) {
                if (!is_null($log->custom_price) && !is_null($log->commission_percentage)) {
                    $log->custom_commission = $log->custom_price * ($log->commission_percentage / 100);
                    $log->save();
                }
            }
        });
    }

    public function down(): void
    {
        // Optional: nullify the custom_commission (not recommended in production)
        ServiceLog::query()->update(['custom_commission' => null]);
    }
};

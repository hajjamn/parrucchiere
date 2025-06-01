<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ServiceLog;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            $table->unsignedInteger('commission_percentage')->nullable()->after('custom_price');
        });

        // Backfill: copy service.percentage into service_log.commission_percentage
        ServiceLog::with('service')->chunk(100, function ($logs) {
            foreach ($logs as $log) {
                if ($log->service) {
                    $log->commission_percentage = $log->service->percentage;
                    $log->save();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            $table->dropColumn('commission_percentage');
        });
    }
};


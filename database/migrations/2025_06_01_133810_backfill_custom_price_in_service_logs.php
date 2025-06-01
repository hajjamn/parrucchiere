<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ServiceLog;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {

            ServiceLog::with('service')->whereNull('custom_price')->chunk(100, function ($logs) {
                foreach ($logs as $log) {
                    if ($log->service) {
                        $log->custom_price = $log->service->price;
                        $log->save();
                    }
                }
            });

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            //
        });
    }
};

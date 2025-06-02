<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Step 1: Add the new column
        Schema::table('service_logs', function (Blueprint $table) {
            $table->unsignedInteger('commission_percentage')->nullable()->after('custom_price');
        });
    }

    public function down(): void
    {
        Schema::table('service_logs', function (Blueprint $table) {
            $table->dropColumn('commission_percentage');
        });
    }
};

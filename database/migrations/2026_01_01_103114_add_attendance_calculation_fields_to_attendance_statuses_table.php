<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_statuses', function (Blueprint $table) {
            $table->boolean('counts_as_present')->default(false)->after('color');
            $table->boolean('is_excluded')->default(false)->after('counts_as_present');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_statuses', function (Blueprint $table) {
            $table->dropColumn(['counts_as_present', 'is_excluded']);
        });
    }
};
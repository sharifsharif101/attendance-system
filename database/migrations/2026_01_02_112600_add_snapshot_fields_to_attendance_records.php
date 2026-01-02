<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            // حفظ نسخة من إعدادات الحالة وقت التسجيل
            $table->boolean('is_excluded_snapshot')->default(false)->after('notes');
            $table->boolean('counts_as_present_snapshot')->default(false)->after('is_excluded_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn(['is_excluded_snapshot', 'counts_as_present_snapshot']);
        });
    }
};

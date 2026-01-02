<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_status_id')->constrained()->cascadeOnDelete();
            $table->string('status_code'); // رمز الحالة للرجوع إليه
            $table->boolean('old_is_excluded');
            $table->boolean('new_is_excluded');
            $table->boolean('old_counts_as_present');
            $table->boolean('new_counts_as_present');
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable(); // سبب التغيير (اختياري)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_status_history');
    }
};

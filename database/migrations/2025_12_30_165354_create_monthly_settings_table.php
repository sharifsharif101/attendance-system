<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_settings', function (Blueprint $table) {
            $table->id();
            $table->string('month', 7)->unique(); // مثل: 2025-10
            $table->text('weekend_days'); // أيام الإجازة JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_settings');
    }
};
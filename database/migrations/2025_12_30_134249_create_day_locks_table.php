<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('day_locks', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('locked_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('locked_at');
            $table->timestamps();

            // منع تكرار قفل نفس اليوم لنفس القسم
            $table->unique(['date', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('day_locks');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // اسم الحالة (حاضر، غائب...)
            $table->string('code')->unique(); // رمز الحالة (present, absent...)
            $table->string('color')->default('#6b7280'); // لون الحالة
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);   // ترتيب العرض
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_statuses');
    }
};
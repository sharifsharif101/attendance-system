<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // البيانات الشخصية
            $table->string('national_id')->nullable()->after('department_id');
            $table->string('nationality')->nullable()->after('national_id');
            $table->date('birth_date')->nullable()->after('nationality');
            $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('gender');
            $table->string('phone')->nullable()->after('marital_status');
            $table->string('email')->nullable()->after('phone');
            
            // بيانات الوثائق
            $table->string('passport_number')->nullable()->after('email');
            $table->date('passport_expiry')->nullable()->after('passport_number');
            $table->string('residency_number')->nullable()->after('passport_expiry');
            $table->date('residency_expiry')->nullable()->after('residency_number');
            
            // بيانات العمل
            $table->date('hire_date')->nullable()->after('residency_expiry');
            $table->string('job_title')->nullable()->after('hire_date');
            $table->enum('contract_type', ['permanent', 'temporary', 'probation'])->nullable()->after('job_title');
            $table->date('contract_expiry')->nullable()->after('contract_type');
            
            // صورة الموظف
            $table->string('photo')->nullable()->after('contract_expiry');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'national_id',
                'nationality',
                'birth_date',
                'gender',
                'marital_status',
                'phone',
                'email',
                'passport_number',
                'passport_expiry',
                'residency_number',
                'residency_expiry',
                'hire_date',
                'job_title',
                'contract_type',
                'contract_expiry',
                'photo',
            ]);
        });
    }
};
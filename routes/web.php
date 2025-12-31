<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceStatusController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;



Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // الملف الشخصي
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // مسارات الحضور
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->middleware('permission:attendance.view')
        ->name('attendance.index');
    
    Route::post('/attendance', [AttendanceController::class, 'store'])
        ->middleware('permission:attendance.create')
        ->name('attendance.store');
    
    Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore'])
        ->middleware('permission:attendance.create')
        ->name('attendance.bulk');
    
    Route::post('/attendance/store-all', [AttendanceController::class, 'storeAll'])
        ->middleware('permission:attendance.create')
        ->name('attendance.storeAll');
    
    Route::post('/attendance/lock', [AttendanceController::class, 'lockDay'])
        ->middleware('permission:attendance.lock')
        ->name('attendance.lock');
    
    Route::post('/attendance/unlock', [AttendanceController::class, 'unlockDay'])
        ->middleware('permission:attendance.unlock')
        ->name('attendance.unlock');

    // سجل التدقيق
    Route::get('/audit', [AuditController::class, 'index'])
        ->middleware('permission:audit.view')
        ->name('audit.index');

    // التقارير
    Route::get('/reports/daily', [ReportController::class, 'daily'])
        ->middleware('permission:reports.view')
        ->name('reports.daily');

    Route::get('/reports/monthly', [ReportController::class, 'monthly'])
        ->middleware('permission:reports.view')
        ->name('reports.monthly');

    // الإعدادات
    Route::get('/settings', [SettingController::class, 'index'])
        ->middleware('permission:users.manage')
        ->name('settings.index');

    Route::post('/settings', [SettingController::class, 'update'])
        ->middleware('permission:users.manage')
        ->name('settings.update');

    Route::post('/settings/month', [SettingController::class, 'updateMonth'])
        ->middleware('permission:users.manage')
        ->name('settings.updateMonth');

    Route::post('/settings/reset-month', [SettingController::class, 'resetMonth'])
        ->middleware('permission:users.manage')
        ->name('settings.resetMonth');

    // إدارة المستخدمين
    Route::resource('users', UserController::class)
        ->middleware('permission:users.manage');

    // حالات الحضور
    Route::resource('statuses', AttendanceStatusController::class)
        ->middleware('permission:users.manage');

    // إدارة الأقسام
    Route::resource('departments', DepartmentController::class)
        ->middleware('permission:departments.manage');

    // إدارة الموظفين
    Route::resource('employees', EmployeeController::class)
        ->middleware('permission:departments.manage');

        Route::resource('roles', RoleController::class)
    ->middleware('permission:roles.manage');
});

require __DIR__.'/auth.php';
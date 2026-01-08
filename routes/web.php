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
use App\Http\Controllers\EmployeeReportController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\QrDisplayController;
use App\Http\Controllers\SelfAttendanceController;



Route::get('/', function () {
    return redirect()->route('welcome');
});

Route::get('/welcome', [WelcomeController::class, 'index'])
    ->middleware(['auth'])
    ->name('welcome');

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
    Route::post('/attendance/ajax-bulk', [AttendanceController::class, 'ajaxBulkStore'])->name('attendance.ajax.bulk');
    
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

Route::get('/audit/{activity}', [AuditController::class, 'show'])
    ->middleware('permission:audit.view')
    ->name('audit.show');

    // التقارير
    Route::get('/reports/daily', [ReportController::class, 'daily'])
        ->middleware('permission:reports.view')
        ->name('reports.daily');

    Route::get('/reports/monthly', [ReportController::class, 'monthly'])
        ->middleware('permission:reports.view')
        ->name('reports.monthly');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])
        ->middleware('permission:users.manage')
        ->name('settings.index');

    Route::post('/settings', [SettingController::class, 'update'])
        ->middleware('permission:users.manage')
        ->name('settings.update');

    Route::resource('settings/holidays', \App\Http\Controllers\OfficialHolidayController::class);
    
    // Monthly Settings
    Route::post('/settings/month', [SettingController::class, 'updateMonth'])
        ->middleware('permission:users.manage')
        ->name('settings.updateMonth');

    Route::post('/settings/reset-month', [SettingController::class, 'resetMonth'])
        ->middleware('permission:users.manage')
        ->name('settings.resetMonth');

    // Database Backup
    Route::get('/backup/download', [BackupController::class, 'download'])
        ->middleware('permission:users.manage')
        ->name('backup.download');
        
    Route::get('/backup/files', [BackupController::class, 'downloadFiles'])
        ->middleware('permission:users.manage')
        ->name('backup.files');

    // إدارة المستخدمين
    Route::resource('users', UserController::class)
        ->middleware('permission:users.manage');

    // حالات الحضور
    Route::resource('statuses', AttendanceStatusController::class)
        ->middleware('permission:users.manage');
    
    // تاريخ تغييرات حالات الحضور
    Route::get('/statuses-history', [AttendanceStatusController::class, 'history'])
        ->middleware('permission:users.manage')
        ->name('statuses.history');
    
    Route::get('/statuses/{status}/history', [AttendanceStatusController::class, 'statusHistory'])
        ->middleware('permission:users.manage')
        ->name('statuses.status-history');

    // إدارة الأقسام
    Route::resource('departments', DepartmentController::class)
        ->middleware('permission:departments.manage');

    // إدارة الموظفين
    Route::resource('employees', EmployeeController::class)
        ->middleware('permission:departments.manage');

        Route::resource('roles', RoleController::class)
    ->middleware('permission:roles.manage');
    Route::get('/reports/employee-search', [EmployeeReportController::class, 'search'])
    ->middleware(['auth', 'permission:reports.view'])
    ->name('reports.employee.search');
    Route::get('/employee/{employee}/report', [EmployeeReportController::class, 'show'])
    ->middleware(['auth', 'permission:reports.view'])
    ->name('employee.report');
    Route::get('/documents/expiring', [EmployeeController::class, 'expiringDocuments'])
    ->middleware(['auth', 'permission:departments.manage'])
    ->name('documents.expiring');
});

// مسارات شاشة عرض QR (تحتاج تسجيل دخول)
Route::middleware(['auth', 'permission:attendance.create'])->prefix('qr')->group(function () {
    Route::get('/display', [QrDisplayController::class, 'show'])->name('qr.display');
    Route::post('/generate', [QrDisplayController::class, 'generate'])->name('qr.generate');
});

// مسارات التسجيل الذاتي (عامة - بدون تسجيل دخول)
Route::prefix('attend')->group(function () {
    Route::get('/{token}', [SelfAttendanceController::class, 'showForm'])->name('attend.form');
    Route::post('/{token}', [SelfAttendanceController::class, 'submit'])->name('attend.submit');
    Route::get('/success/done', [SelfAttendanceController::class, 'success'])->name('attend.success');
});

require __DIR__.'/auth.php';
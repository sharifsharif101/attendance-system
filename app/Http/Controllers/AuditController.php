<?php

namespace App\Http\Controllers;
// جودة
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditController extends Controller
{

    public function index(Request $request)
    {
        // الفلاتر
        $userId = $request->get('user_id');
        $event = $request->get('event');
        $subjectType = $request->get('subject_type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $searchName = $request->get('search_name');

        $activities = Activity::with([
            'causer' => function (MorphTo $morphTo) {
                $morphTo->constrain([
                    User::class => function ($q) { $q->withTrashed(); },
                ]);
            },
            'subject' => function (MorphTo $morphTo) {
                $morphTo->constrain([
                    \App\Models\Employee::class => function ($q) { $q->withTrashed(); },
                    User::class => function ($q) { $q->withTrashed(); },
                    \App\Models\AttendanceStatus::class => function ($q) { $q->withTrashed(); },
                ]);
            }
        ])
            ->when($searchName, function($q) use ($searchName) {
                // البحث عن الموظفين بهذا الاسم
                $employeeIds = \App\Models\Employee::withTrashed()->where('name', 'like', "%{$searchName}%")->pluck('id');
                // البحث عن سجلات الحضور لهؤلاء الموظفين
                $attendanceRecordIds = \App\Models\AttendanceRecord::whereIn('employee_id', $employeeIds)->pluck('id');

                $q->where(function($query) use ($employeeIds, $attendanceRecordIds) {
                    // السجل مرتبط بموظف مباشرة
                    $query->where(function($sub) use ($employeeIds) {
                        $sub->where('subject_type', 'App\Models\Employee')
                            ->whereIn('subject_id', $employeeIds);
                    })
                    // السجل مرتبط بسجل حضور تابع للموظف
                    ->orWhere(function($sub) use ($attendanceRecordIds) {
                        $sub->where('subject_type', 'App\Models\AttendanceRecord')
                            ->whereIn('subject_id', $attendanceRecordIds);
                    });
                });
            })
            ->when($userId, function($q) use ($userId) {
                $q->where('causer_id', $userId);
            })
            ->when($event, function($q) use ($event) {
                $q->where('event', $event);
            })
            ->when($subjectType, function($q) use ($subjectType) {
                $q->where('subject_type', $subjectType);
            })
            ->when($dateFrom, function($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // بيانات الفلاتر
        $users = User::withTrashed()->orderBy('name')->get();
        
        $events = [
            'created' => 'إنشاء',
            'updated' => 'تعديل',
            'deleted' => 'حذف',
        ];
        
        $subjectTypes = [
            'App\Models\AttendanceRecord' => 'سجل حضور',
            'App\Models\Employee' => 'موظف',
            'App\Models\Department' => 'قسم',
            'App\Models\User' => 'مستخدم',
            'App\Models\AttendanceStatus' => 'حالة حضور',
        ];

        return view('audit.index', compact(
            'activities', 
            'users', 
            'events', 
            'subjectTypes',
            'userId',
            'event',
            'subjectType',
            'dateFrom',
            'dateTo',
            'searchName'
        ));
    }

    // عرض تفاصيل سجل واحد
    public function show(Activity $activity)
    {
        $activity->load(['causer', 'subject']);
        
        return response()->json([
            'id' => $activity->id,
            'event' => $activity->event,
            'description' => $activity->description,
            'causer' => $activity->causer?->name ?? 'النظام',
            'subject_type' => class_basename($activity->subject_type),
            'subject_id' => $activity->subject_id,
            'properties' => $activity->properties,
            'old' => $activity->properties['old'] ?? null,
            'attributes' => $activity->properties['attributes'] ?? null,
            'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
            'time_ago' => $activity->created_at->diffForHumans(),
        ]);
    }
}
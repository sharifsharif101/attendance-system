<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

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

        $activities = Activity::with(['causer', 'subject'])
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
        $users = User::orderBy('name')->get();
        
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
            'dateTo'
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
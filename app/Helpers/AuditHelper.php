<?php

namespace App\Helpers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\User;

class AuditHelper
{
    public static function getEventLabel($event)
    {
        return match ($event) {
            'created' => 'إضافة جديد',
            'updated' => 'تعديل بيانات',
            'deleted' => 'حذف سجل',
            default => $event,
        };
    }

    public static function getEventColor($event)
    {
        return match ($event) {
            'created' => 'green',
            'updated' => 'yellow',
            'deleted' => 'red',
            default => 'gray',
        };
    }

    public static function formatValue($key, $value)
    {
        if (is_bool($value)) {
            return $value ? 'نعم' : 'لا';
        }

        if ($key === 'status') {
             $statuses = [
                'present' => 'حاضر',
                'absent' => 'غائب',
                'late' => 'متأخر',
                'leave' => 'إجازة',
            ];
            return $statuses[$value] ?? $value;
        }
        
        if (str_contains($key, 'date') && $value) {
             return date('Y-m-d', strtotime($value));
        }

        return $value;
    }

    public static function getFieldLabel($key)
    {
        $labels = [
            'status' => 'الحالة',
            'check_in_time' => 'وقت الدخول',
            'check_out_time' => 'وقت الخروج',
            'notes' => 'ملاحظات',
            'is_excluded' => 'مستبعد',
            'counts_as_present' => 'يحتسب حضور',
            'created_at' => 'تاريخ الإنشاء',
            'updated_at' => 'تاريخ التعديل',
            'employee_id' => 'الموظف',
            'date' => 'تاريخ السجل',
            'counts_as_present_snapshot' => 'هل يُحسب كحضور؟',
            'is_excluded_snapshot' => 'هل اليوم مستبعد من الاحتساب؟',
            'recorded_by' => 'المسجل (ID)',
            'department_id' => 'القسم',
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'is_active' => 'نشط',
        ];

        return $labels[$key] ?? $key;
    }

    /**
     * getSubjectSummary
     * وصف مقروء للشيء الذي تم تعديله
     */
    public static function getSubjectSummary($activity)
    {
        $subject = $activity->subject;

        if (!$subject) {
            // المحاولة من خلال الخصائص إذا كان محذوفاً
            if ($activity->subject_type === 'App\Models\AttendanceRecord') {
                 return 'سجل حضور (محذوف)';
            }
            return class_basename($activity->subject_type) . ' #' . $activity->subject_id;
        }

        if ($subject instanceof AttendanceRecord) {
            // محاولة جلب اسم الموظف
            // يفترض أن العلاقة employee موجودة
            $empName = $subject->employee?->name ?? 'موظف غير معروف';
            $empNum = $subject->employee?->employee_number ?? '---';
            $date = $subject->date ? $subject->date->format('Y-m-d') : '';
            return "سجل حضور: {$empName} ({$empNum}) - {$date}";
        }

        if ($subject instanceof Employee) {
            return "ملف الموظف: {$subject->name} ({$subject->employee_number})";
        }

        if ($subject instanceof User) {
            return "المستخدم: {$subject->name}";
        }

        return class_basename($activity->subject_type) . " #{$activity->subject_id}";
    }
}

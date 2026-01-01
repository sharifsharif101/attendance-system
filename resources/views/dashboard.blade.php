<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="flex">
            
            {{-- المحتوى الرئيسي --}}
            <div class="flex-1 p-6">
                
                {{-- الهيدر مع البحث --}}
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                            مرحباً، {{ Auth::user()->name }} 👋
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ now()->translatedFormat('l، d F Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                 
                    </div>
                </div>

                {{-- البانر الرئيسي --}}
                <div class="bg-gradient-to-l from-blue-600 via-blue-500 to-purple-600 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
                    <div class="absolute left-0 top-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute left-20 bottom-0 w-32 h-32 bg-white/10 rounded-full translate-y-1/2"></div>
                    <div class="relative z-10">
                        <p class="text-blue-100 text-sm mb-2">نظام الحضور والغياب</p>
                        <h2 class="text-2xl font-bold mb-4">إدارة حضور الموظفين<br>بكل سهولة وفعالية</h2>
                        <a href="{{ route('attendance.index') }}" 
                            class="inline-flex items-center gap-2 bg-white text-blue-600 px-5 py-2 rounded-full text-sm font-bold hover:bg-blue-50 transition">
                            تسجيل الحضور
                            <span>←</span>
                        </a>
                    </div>
                </div>

                {{-- الإحصائيات السريعة --}}
                <div class="flex gap-4 mb-6 overflow-x-auto pb-2">
                    <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl px-5 py-3 shadow-sm min-w-fit">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-lg">👥</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">الموظفين</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $totalEmployees }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl px-5 py-3 shadow-sm min-w-fit">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-lg">🏢</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">الأقسام</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $totalDepartments }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl px-5 py-3 shadow-sm min-w-fit">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-lg">📊</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">نسبة الحضور</p>
                            <p class="text-lg font-bold {{ $todayAttendanceRate >= 80 ? 'text-green-600' : ($todayAttendanceRate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $todayAttendanceRate }}%</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl px-5 py-3 shadow-sm min-w-fit">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-lg">👤</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">المستخدمين</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>

                {{-- إحصائيات اليوم --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">📅 إحصائيات اليوم</h3>
                        <a href="{{ route('reports.daily') }}" class="text-sm text-blue-500 hover:underline">عرض التقرير ←</a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        @foreach($statuses as $status)
                            @php $count = $todayStats[$status->code] ?? 0; @endphp
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-10 rounded-full" style="background-color: {{ $status->color }}"></div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $count }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $status->name }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- نسبة الحضور حسب القسم --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">🏢 الأقسام</h3>
                        <a href="{{ route('departments.index') }}" class="text-sm text-blue-500 hover:underline">عرض الكل ←</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-right text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                                    <th class="pb-3 font-medium">القسم</th>
                                    <th class="pb-3 font-medium">الموظفين</th>
                                    <th class="pb-3 font-medium">نسبة الحضور</th>
                                    <th class="pb-3 font-medium">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($departmentStats->take(5) as $dept)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <td class="py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                                    {{ mb_substr($dept->name, 0, 1) }}
                                                </div>
                                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $dept->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 text-gray-600 dark:text-gray-400">{{ $dept->total_employees }} موظف</td>
                                        <td class="py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-20 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $dept->attendance_rate >= 80 ? 'bg-green-500' : ($dept->attendance_rate >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                        style="width: {{ $dept->attendance_rate }}%"></div>
                                                </div>
                                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $dept->attendance_rate }}%</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if($dept->attendance_rate >= 80)
                                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 rounded-full text-xs">ممتاز</span>
                                            @elseif($dept->attendance_rate >= 50)
                                                <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/50 text-yellow-600 dark:text-yellow-400 rounded-full text-xs">متوسط</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 rounded-full text-xs">ضعيف</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- الشريط الجانبي الأيمن --}}
            <div class="w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 p-6 hidden lg:block">
                
                {{-- بطاقة المستخدم --}}
                <div class="text-center mb-6">
                    <div class="relative inline-block">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto">
                            {{ mb_substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -left-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center">
                            <span class="text-white text-xs">✓</span>
                        </div>
                    </div>
                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mt-3">{{ Auth::user()->name }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->roles->first()->name ?? 'مستخدم' }}</p>
                </div>

                {{-- تنبيهات الوثائق --}}
                @if($expiredDocuments > 0 || $expiringPassports > 0 || $expiringResidencies > 0 || $expiringContracts > 0)
                <div class="mb-6">
                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-3">⚠️ تنبيهات</h4>
                    <div class="space-y-2">
                        @if($expiredDocuments > 0)
                        <a href="{{ route('documents.expiring', ['type' => 'expired']) }}" 
                            class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                            <div class="flex items-center gap-2">
                                <span class="text-red-500">🔴</span>
                                <span class="text-sm text-gray-700 dark:text-gray-300">وثائق منتهية</span>
                            </div>
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $expiredDocuments }}</span>
                        </a>
                        @endif
                        
                        @if($expiringPassports > 0)
                        <a href="{{ route('documents.expiring', ['type' => 'passport']) }}" 
                            class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition">
                            <div class="flex items-center gap-2">
                                <span class="text-yellow-500">🛂</span>
                                <span class="text-sm text-gray-700 dark:text-gray-300">جوازات تنتهي</span>
                            </div>
                            <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">{{ $expiringPassports }}</span>
                        </a>
                        @endif
                        
                        @if($expiringResidencies > 0)
                        <a href="{{ route('documents.expiring', ['type' => 'residency']) }}" 
                            class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl hover:bg-orange-100 dark:hover:bg-orange-900/30 transition">
                            <div class="flex items-center gap-2">
                                <span class="text-orange-500">🏠</span>
                                <span class="text-sm text-gray-700 dark:text-gray-300">إقامات تنتهي</span>
                            </div>
                            <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $expiringResidencies }}</span>
                        </a>
                        @endif
                        
                        @if($expiringContracts > 0)
                        <a href="{{ route('documents.expiring', ['type' => 'contract']) }}" 
                            class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                            <div class="flex items-center gap-2">
                                <span class="text-blue-500">📝</span>
                                <span class="text-sm text-gray-700 dark:text-gray-300">عقود تنتهي</span>
                            </div>
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ $expiringContracts }}</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- آخر السجلات --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-gray-800 dark:text-gray-200">🕐 آخر السجلات</h4>
                        <a href="{{ route('audit.index') }}" class="text-xs text-blue-500 hover:underline">الكل</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentRecords->take(4) as $record)
                            @php
                                $statusData = $statuses->firstWhere('code', $record->status);
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background-color: {{ $statusData->color ?? '#6b7280' }}">
                                    {{ mb_substr($record->employee->name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $record->employee->name ?? 'غير معروف' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $statusData->name ?? $record->status }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 dark:text-gray-400 text-sm py-4">لا توجد سجلات</p>
                        @endforelse
                    </div>
                </div>

                {{-- روابط سريعة --}}
                <div>
                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-3">⚡ وصول سريع</h4>
                    <div class="space-y-2">
                        <a href="{{ route('attendance.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <span class="text-lg">📅</span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">تسجيل الحضور</span>
                        </a>
                        <a href="{{ route('reports.monthly') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <span class="text-lg">📊</span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">التقرير الشهري</span>
                        </a>
                        <a href="{{ route('employees.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <span class="text-lg">👥</span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">الموظفين</span>
                        </a>
                        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <span class="text-lg">⚙️</span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">الإعدادات</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
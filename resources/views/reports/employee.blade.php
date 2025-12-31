<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📋 تقرير الموظف: {{ $employee->name }}
            </h2>
            <button onclick="window.print()" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded print:hidden">
                🖨️ طباعة
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- فلترة الشهر --}}
            <div class="mb-6 print:hidden">
                <form method="GET" action="{{ route('employee.report', $employee) }}" class="flex gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الشهر</label>
                        <input type="month" name="month" value="{{ $month }}" 
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
                    </div>
                    <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        عرض
                    </button>
                    <a href="{{ route('employees.index') }}" 
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        ← رجوع
                    </a>
                </form>
            </div>

            {{-- معلومات الموظف --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">👤 معلومات الموظف</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">الاسم</p>
                            <p class="font-bold text-gray-900 dark:text-gray-100">{{ $employee->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">الرقم الوظيفي</p>
                            <p class="font-bold text-gray-900 dark:text-gray-100">{{ $employee->employee_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">القسم</p>
                            <p class="font-bold text-gray-900 dark:text-gray-100">{{ $employee->department->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">الحالة</p>
                            @if($employee->is_active)
                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded text-sm">مفعّل</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 rounded text-sm">معطّل</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ملخص الشهر --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        📊 ملخص شهر {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        {{-- أيام العمل --}}
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-3xl font-bold text-blue-500">{{ $workingDays }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">أيام العمل</p>
                        </div>
                        
                        {{-- نسبة الانضباط --}}
                        <div class="text-center p-4 rounded-lg border
                            @if($attendanceRate >= 90) bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800
                            @elseif($attendanceRate >= 70) bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800
                            @else bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800
                            @endif">
                            <p class="text-3xl font-bold 
                                @if($attendanceRate >= 90) text-green-500
                                @elseif($attendanceRate >= 70) text-yellow-500
                                @else text-red-500
                                @endif">{{ $attendanceRate }}%</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">نسبة الانضباط</p>
                        </div>

                        {{-- الحالات --}}
                        @foreach($summary as $code => $data)
                            <div class="text-center p-4 rounded-lg" style="background-color: {{ $data['color'] }}15; border: 1px solid {{ $data['color'] }}40;">
                                <p class="text-3xl font-bold" style="color: {{ $data['color'] }}">{{ $data['count'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $data['name'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- سجل الحضور التفصيلي --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📅 سجل الحضور التفصيلي</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">التاريخ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">اليوم</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الحالة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ملاحظات</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">المسجّل</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($records as $record)
                                    @php
                                        $statusData = $statuses->firstWhere('code', $record->status);
                                        $dayName = \Carbon\Carbon::parse($record->date)->translatedFormat('l');
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $record->date }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $dayName }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded text-white text-sm" 
                                                style="background-color: {{ $statusData->color ?? '#6b7280' }}">
                                                {{ $statusData->name ?? $record->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $record->notes ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $record->recorder->name ?? 'النظام' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            لا توجد سجلات لهذا الشهر
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- الإحصائيات السنوية --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        📈 الإحصائيات السنوية ({{ \Carbon\Carbon::parse($month)->year }})
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الشهر</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">أيام العمل</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">أيام الحضور</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">أيام الغياب</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">نسبة الانضباط</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($yearlyStats as $stat)
                                    <tr class="{{ $stat['month'] === $month ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $stat['month_name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">
                                            {{ $stat['working_days'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-green-600 dark:text-green-400">
                                            {{ $stat['present_days'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-red-600 dark:text-red-400">
                                            {{ $stat['absent_days'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-3 py-1 rounded text-white font-bold text-sm
                                                @if($stat['attendance_rate'] >= 90) bg-green-500
                                                @elseif($stat['attendance_rate'] >= 70) bg-yellow-500
                                                @else bg-red-500
                                                @endif">
                                                {{ $stat['attendance_rate'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- أنماط الطباعة --}}
    <style>
        @media print {
            body { background: white !important; }
            .print\:hidden { display: none !important; }
            .shadow-sm { box-shadow: none !important; }
            .dark\:bg-gray-800 { background: white !important; }
            .dark\:text-gray-100, .dark\:text-gray-200, .dark\:text-gray-300, .dark\:text-gray-400 { color: black !important; }
        }
    </style>
</x-app-layout>
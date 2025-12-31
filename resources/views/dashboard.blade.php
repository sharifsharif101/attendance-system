<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            لوحة التحكم
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- الإحصائيات العامة --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                {{-- إجمالي الموظفين --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-20">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="mr-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي الموظفين</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalEmployees }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- إجمالي الأقسام --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-20">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="mr-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي الأقسام</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalDepartments }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- نسبة الحضور اليوم --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20">
                                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="mr-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">نسبة الحضور اليوم</p>
                                <p class="text-2xl font-bold @if($todayAttendanceRate >= 90) text-green-500 @elseif($todayAttendanceRate >= 70) text-yellow-500 @else text-red-500 @endif">
                                    {{ $todayAttendanceRate }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- إجمالي المستخدمين --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 bg-opacity-20">
                                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="mr-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المستخدمين</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalUsers }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- إحصائيات اليوم --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- حالة اليوم --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            📅 إحصائيات اليوم ({{ $today }})
                        </h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-3xl font-bold text-blue-500">{{ $todayRecords }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي السجلات</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-3xl font-bold text-green-500">{{ $todayPresent }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">حاضر</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-3xl font-bold text-red-500">{{ $todayAbsent }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">غائب</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- إحصائيات الشهر --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            📊 إحصائيات الشهر ({{ $currentMonth }})
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($statuses as $status)
                                <div class="text-center p-3 rounded-lg" style="background-color: {{ $status->color }}20;">
                                    <p class="text-2xl font-bold" style="color: {{ $status->color }}">
                                        {{ $monthlyStats[$status->code] ?? 0 }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $status->name }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- إحصائيات الأقسام وآخر السجلات --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- نسبة الحضور حسب القسم --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            🏢 نسبة الحضور حسب القسم
                        </h3>
                        <div class="space-y-4">
                            @foreach($departmentStats as $dept)
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $dept->name }}</span>
                                        <span class="text-sm font-bold @if($dept->attendance_rate >= 90) text-green-500 @elseif($dept->attendance_rate >= 70) text-yellow-500 @else text-red-500 @endif">
                                            {{ $dept->attendance_rate }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full @if($dept->attendance_rate >= 90) bg-green-500 @elseif($dept->attendance_rate >= 70) bg-yellow-500 @else bg-red-500 @endif" 
                                            style="width: {{ $dept->attendance_rate }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $dept->total_employees }} موظف</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- آخر السجلات --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            🕐 آخر السجلات
                        </h3>
                        <div class="space-y-3">
                            @forelse($recentRecords as $record)
                                @php
                                    $statusData = $statuses->firstWhere('code', $record->status);
                                @endphp
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $record->employee->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $record->date }} - {{ $record->recorder?->name ?? 'النظام' }}</p>
                                    </div>
                                    <span class="px-2 py-1 rounded text-white text-xs" 
                                        style="background-color: {{ $statusData->color ?? '#6b7280' }}">
                                        {{ $statusData->name ?? $record->status }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center">لا توجد سجلات</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
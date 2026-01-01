<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            التقرير الشهري
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- فلترة --}}
                    <form method="GET" action="{{ route('reports.monthly') }}" class="mb-6">
                        <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الشهر</label>
                                <input type="month" name="month" value="{{ $month }}" 
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">القسم</label>
                                <select name="department_id" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
                                    <option value="">اختر القسم</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" 
                                            {{ $departmentId == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                    عرض التقرير
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($departmentId && $employees->count() > 0)
                        {{-- جدول التقرير الشهري --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الموظف</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">أيام العمل</th>
                                        @foreach($statuses as $status)
                                            <th class="px-4 py-3 text-center text-xs font-medium uppercase" 
                                                style="color: {{ $status->color }}">
                                                {{ $status->name }}
                                            </th>
                                        @endforeach
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">نسبة الانضباط</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($employees as $employee)
                                        @php
                                            $stats = $summary[$employee->id];
                                            $rate = $stats['attendance_rate'];
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                <div>{{ $employee->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->employee_number }}</div>
                                            </td>
                                            <td class="px-4 py-4 text-center font-bold text-gray-900 dark:text-gray-100">
                                                {{ $workingDays }}
                                            </td>
                                            @foreach($statuses as $status)
                                                @php $count = $stats[$status->code] ?? 0; @endphp
                                                <td class="px-4 py-4 text-center">
                                                    @if($count > 0)
                                                        <span class="px-2 py-1 rounded text-white text-sm" 
                                                            style="background-color: {{ $status->color }}">
                                                            {{ $count }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-300 dark:text-gray-600">0</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="px-4 py-4 text-center">
                                                <span class="px-3 py-1 rounded text-white font-bold
                                                    @if($rate >= 90) bg-green-500
                                                    @elseif($rate >= 70) bg-yellow-500
                                                    @else bg-red-500
                                                    @endif">
                                                    {{ $rate }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($departmentId)
                        <p class="text-gray-500 dark:text-gray-400">لا يوجد موظفين في هذا القسم</p>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">اختر القسم واضغط عرض التقرير</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
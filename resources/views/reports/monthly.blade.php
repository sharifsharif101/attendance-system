<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            التقرير الشهري
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- فلترة --}}
                    <form method="GET" action="{{ route('reports.monthly') }}" class="mb-6">
                        <div class="flex flex-row gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">الشهر</label>
                                <input type="month" name="month" value="{{ $month }}" 
                                    class="rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">القسم</label>
                                <select name="department_id" class="rounded-md border-gray-300 shadow-sm">
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
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">أيام العمل</th>
                                        @foreach($statuses as $status)
                                            <th class="px-4 py-3 text-center text-xs font-medium uppercase" 
                                                style="color: {{ $status->color }}">
                                                {{ $status->name }}
                                            </th>
                                        @endforeach
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">نسبة الانضباط</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($employees as $employee)
                                        @php
                                            $stats = $summary[$employee->id];
                                            $rate = $stats['attendance_rate'];
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div>{{ $employee->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $employee->employee_number }}</div>
                                            </td>
                                            <td class="px-4 py-4 text-center font-bold">
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
                                                        <span class="text-gray-300">0</span>
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
                        <p class="text-gray-500">لا يوجد موظفين في هذا القسم</p>
                    @else
                        <p class="text-gray-500">اختر القسم واضغط عرض التقرير</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
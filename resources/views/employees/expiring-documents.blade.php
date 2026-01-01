<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ⚠️ تقرير الوثائق المنتهية والمنتهية قريباً
            </h2>
            <button onclick="window.print()"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded print:hidden text-center">
                🖨️ طباعة
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- فلاتر --}}
                    <div class="mb-6 flex flex-wrap gap-2 print:hidden">
                        <a href="{{ route('documents.expiring') }}"
                            class="px-4 py-2 rounded-lg {{ $type === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            📋 الكل
                        </a>
                        <a href="{{ route('documents.expiring', ['type' => 'expired']) }}"
                            class="px-4 py-2 rounded-lg {{ $type === 'expired' ? 'bg-red-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            🔴 منتهية
                        </a>
                        <a href="{{ route('documents.expiring', ['type' => 'passport']) }}"
                            class="px-4 py-2 rounded-lg {{ $type === 'passport' ? 'bg-yellow-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            🛂 جوازات
                        </a>
                        <a href="{{ route('documents.expiring', ['type' => 'residency']) }}"
                            class="px-4 py-2 rounded-lg {{ $type === 'residency' ? 'bg-orange-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            🏠 إقامات
                        </a>
                        <a href="{{ route('documents.expiring', ['type' => 'contract']) }}"
                            class="px-4 py-2 rounded-lg {{ $type === 'contract' ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            📝 عقود
                        </a>
                    </div>

                    {{-- عدد النتائج --}}
                    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                        إجمالي النتائج: {{ $employees->count() }}
                    </div>

                    {{-- الجدول --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        الموظف</th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        القسم</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        انتهاء الجواز</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        انتهاء الإقامة</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        انتهاء العقد</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase print:hidden">
                                        إجراء</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($employees as $employee)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if ($employee->photo)
                                                    <img src="{{ asset('storage/' . $employee->photo) }}"
                                                        class="w-8 h-8 rounded-full ml-2 object-cover">
                                                @else
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center ml-2">
                                                        <span class="text-xs">👤</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                                        {{ $employee->name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $employee->employee_number }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td
                                            class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $employee->department->name }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            @if ($employee->passport_expiry)
                                                @php
                                                    $days = (int) now()
                                                        ->startOfDay()
                                                        ->diffInDays($employee->passport_expiry->startOfDay(), false);
                                                @endphp

                                                <span
                                                    class="px-2 py-1 rounded text-xs font-bold
            @if ($days < 0) bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300
            @elseif($days <= 30) bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300
            @else bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 @endif">
                                                    {{ $employee->passport_expiry->format('Y-m-d') }}
                                                    @if ($days < 0)
                                                        (منتهي)
                                                    @else
                                                        ({{ $days }} يوم)
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            @if ($employee->residency_expiry)
                                                @php
                                                    $days = (int) now()
                                                        ->startOfDay()
                                                        ->diffInDays($employee->residency_expiry->startOfDay(), false);
                                                @endphp
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-bold
                                                    @if ($days < 0) bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300
                                                    @elseif($days <= 60) bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300
                                                    @else bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 @endif">
                                                    {{ $employee->residency_expiry->format('Y-m-d') }}
                                                    @if ($days < 0)
                                                        (منتهية)
                                                    @else
                                                        ({{ $days }} يوم)
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            @if ($employee->contract_expiry)
                                                @php
                                                    $days = (int) now()
                                                        ->startOfDay()
                                                        ->diffInDays($employee->contract_expiry->startOfDay(), false);
                                                @endphp

                                                <span
                                                    class="px-2 py-1 rounded text-xs font-bold
                                                    @if ($days < 0) bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300
                                                    @elseif($days <= 30) bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300
                                                    @else bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 @endif">
                                                    {{ $employee->contract_expiry->format('Y-m-d') }}
                                                    @if ($days < 0)
                                                        (منتهي)
                                                    @else
                                                        ({{ $days }} يوم)
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center print:hidden">
                                            <a href="{{ route('employees.edit', $employee) }}"
                                                class="text-blue-500 hover:text-blue-700 text-sm">
                                                تعديل
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <div class="text-4xl mb-2">✅</div>
                                            لا توجد وثائق منتهية أو تنتهي قريباً
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</x-app-layout>

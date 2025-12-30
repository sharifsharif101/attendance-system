<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            تسجيل الحضور والغياب
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- رسائل النجاح والخطأ --}}
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- فلترة التاريخ والقسم --}}
                    <form method="GET" action="{{ route('attendance.index') }}" class="mb-6">
                        <div class="flex flex-row gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                                <input type="date" name="date" value="{{ $date }}"
                                    class="rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">القسم</label>
                                <select name="department_id" class="rounded-md border-gray-300 shadow-sm">
                                    <option value="">اختر القسم</option>
                                    @foreach ($departments as $department)
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
                                    عرض
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- التحضير الجماعي --}}
                    @if ($departmentId && !$isLocked && $employees->count() > 0)
                        @can('attendance.create')
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold mb-3">التحضير الجماعي</h3>
                            <form method="POST" action="{{ route('attendance.bulk') }}"
                                class="flex flex-row gap-4 items-end">
                                @csrf
                                <input type="hidden" name="department_id" value="{{ $departmentId }}">
                                <input type="hidden" name="date" value="{{ $date }}">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة للجميع</label>
                               <select name="status" class="rounded-md border-gray-300 shadow-sm">
    @foreach($statuses as $status)
        <option value="{{ $status->code }}">{{ $status->name }}</option>
    @endforeach
</select>
                                </div>

                                <button type="submit"
                                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                    تطبيق على الكل
                                </button>
                            </form>
                        </div>
                        @endcan
                    @endif

                    {{-- حالة القفل وأزرار التحكم --}}
                    @if ($departmentId)
                        @if ($isLocked)
                            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
                                <span>🔒 هذا اليوم مقفل - لا يمكن التعديل</span>
                                @can('attendance.unlock')
                                <form method="POST" action="{{ route('attendance.unlock') }}">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $date }}">
                                    <input type="hidden" name="department_id" value="{{ $departmentId }}">
                                    <button type="submit"
                                        class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-4 rounded text-sm">
                                        فتح اليوم
                                    </button>
                                </form>
                                @endcan
                            </div>
                        @else
                            @can('attendance.lock')
                            <div class="mb-4 flex justify-end">
                                <form method="POST" action="{{ route('attendance.lock') }}">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $date }}">
                                    <input type="hidden" name="department_id" value="{{ $departmentId }}">
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        🔒 قفل اليوم
                                    </button>
                                </form>
                            </div>
                            @endcan
                        @endif
                    @endif

                    {{-- جدول الموظفين --}}
                    @if ($employees->count() > 0)
                        <form method="POST" action="{{ route('attendance.storeAll') }}">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="department_id" value="{{ $departmentId }}">

                            <table class="min-w-full divide-y divide-gray-200 mb-4">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرقم</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($employees as $index => $employee)
                                        @php
                                            $record = $employee->attendanceRecords->first();
                                        @endphp
                                        <tr>
                                            <input type="hidden" name="attendance[{{ $index }}][employee_id]"
                                                value="{{ $employee->id }}">

                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $employee->employee_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $employee->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                <select name="attendance[{{ $index }}][status]"
    class="rounded-md border-gray-300 shadow-sm text-sm"
    {{ $isLocked ? 'disabled' : '' }}>
    @foreach($statuses as $status)
        <option value="{{ $status->code }}" {{ $record?->status == $status->code ? 'selected' : '' }}>
            {{ $status->name }}
        </option>
    @endforeach
</select>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="text" name="attendance[{{ $index }}][notes]"
                                                    value="{{ $record?->notes }}"
                                                    class="rounded-md border-gray-300 shadow-sm text-sm w-full"
                                                    placeholder="ملاحظات..." {{ $isLocked ? 'disabled' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if (!$isLocked)
                                @can('attendance.create')
                                <button type="submit"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                                    حفظ الكل
                                </button>
                                @endcan
                            @endif
                        </form>
                    @elseif($departmentId)
                        <p class="text-gray-500">لا يوجد موظفين في هذا القسم</p>
                    @else
                        <p class="text-gray-500">اختر القسم واضغط عرض</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
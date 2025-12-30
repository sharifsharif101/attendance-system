<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            التقرير اليومي
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- فلترة --}}
                    <form method="GET" action="{{ route('reports.daily') }}" class="mb-6">
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

                    @if($departmentId)
                        {{-- ملخص الإحصائيات --}}
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                            @foreach($summary as $code => $data)
                                <div class="p-4 rounded-lg text-center" style="background-color: {{ $data['color'] }}20; border: 2px solid {{ $data['color'] }}">
                                    <div class="text-2xl font-bold" style="color: {{ $data['color'] }}">{{ $data['count'] }}</div>
                                    <div class="text-sm text-gray-600">{{ $data['name'] }}</div>
                                </div>
                            @endforeach
                        </div>

                        {{-- جدول التفاصيل --}}
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرقم الوظيفي</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($records as $record)
                                    @php
                                        $statusData = $statuses->firstWhere('code', $record->status);
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $record->employee->employee_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $record->employee->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded text-white" 
                                                style="background-color: {{ $statusData->color ?? '#6b7280' }}">
                                                {{ $statusData->name ?? $record->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $record->notes ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            لا توجد سجلات لهذا اليوم
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">اختر القسم واضغط عرض التقرير</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🔍 البحث عن موظف
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- نموذج البحث --}}
                    <form method="GET" action="{{ route('reports.employee.search') }}" class="mb-6">
                        <div class="flex flex-wrap gap-4 items-end">
                            <div class="flex-1 min-w-64">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البحث</label>
                                <input type="text" name="q" value="{{ $query }}" 
                                    id="searchInput"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm"
                                    placeholder="اكتب اسم الموظف أو الرقم الوظيفي..."
                                    autofocus>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">القسم</label>
                                <select name="department_id" 
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
                                    <option value="">كل الأقسام</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ $departmentId == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                    🔍 بحث
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- نتائج البحث --}}
                    @if($query || $departmentId)
                        <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                            عدد النتائج: {{ $employees->count() }}
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الرقم الوظيفي</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الاسم</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">القسم</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($employees as $employee)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $employee->employee_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $employee->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $employee->department->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <a href="{{ route('employee.report', $employee) }}" 
                                                    class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white text-sm font-bold rounded transition">
                                                    📋 عرض التقرير
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                                <div class="text-4xl mb-2">🔍</div>
                                                لا توجد نتائج مطابقة
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <div class="text-6xl mb-4">👤</div>
                            <p class="text-lg">ابحث عن موظف لعرض تقريره</p>
                            <p class="text-sm mt-2">يمكنك البحث بالاسم أو الرقم الوظيفي</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
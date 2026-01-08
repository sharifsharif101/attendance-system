<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            تسجيل الحضور والغياب
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <x-alert />

                    {{-- فلترة التاريخ والقسم --}}
                    <form method="GET" action="{{ route('attendance.index') }}" class="mb-6">
                        <div class="flex flex-wrap gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">التاريخ</label>
                                <input type="date" name="date" value="{{ $date }}"
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">القسم</label>
                                <select name="department_id" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
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

                    {{-- حالة القفل --}}
                    @if ($departmentId)
                        
                        {{-- تنبيه العطلات --}}
                        @if (isset($nonWorkingDayReason) && !empty($nonWorkingDayReason))
                            <div class="bg-blue-100 dark:bg-blue-900 border border-blue-400 dark:border-blue-600 text-blue-700 dark:text-blue-300 px-4 py-3 rounded mb-4 flex items-center gap-2">
                                <span class="text-xl">ℹ️</span>
                                <div>
                                    <span class="font-bold">تنبيه:</span> هذا اليوم مصنف كـ 
                                    <span class="font-bold underline">{{ $nonWorkingDayReason }}</span>.
                                    <span class="text-sm block sm:inline">تم تعطيل خيار "غائب" لمنع الأخطاء.</span>
                                </div>
                            </div>
                        @endif
                        @if ($isLocked)
                            <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded mb-4 flex justify-between items-center">
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
                        
                        {{-- شريط البحث والتحديد --}}
                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex flex-wrap gap-4 items-center justify-between">
                                {{-- البحث --}}
                                <div class="flex-1 min-w-64">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">🔍 البحث</label>
                                    <input type="text" id="searchInput" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm"
                                        placeholder="ابحث بالاسم أو الرقم الوظيفي...">
                                </div>
                                
                                {{-- عداد المحددين --}}
                                <div class="text-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">المحددين:</span>
                                    <span id="selectedCount" class="text-2xl font-bold text-blue-500 mx-2">0</span>
                                </div>
                            </div>
                            
                            {{-- شريط التطبيق الجماعي --}}
                            <div id="bulkActionBar" class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg hidden">
                                <div class="flex flex-wrap gap-3 items-center">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        تطبيق على المحددين (<span id="bulkCount">0</span>):
                                    </span>
                                    <select id="bulkStatus" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm text-sm"
                                        @if(isset($nonWorkingDayReason) && !empty($nonWorkingDayReason)) disabled @endif>
                                        @if(isset($nonWorkingDayReason) && !empty($nonWorkingDayReason))
                                            <option value="" selected>⚠️ يوم عطلة - لا يمكن التسجيل</option>
                                        @else
                                            @foreach($statuses as $status)
                                                <option value="{{ $status->code }}" data-color="{{ $status->color }}">
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="button" id="applyBulkBtn"
                                        class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm"
                                        @if(isset($nonWorkingDayReason) && !empty($nonWorkingDayReason)) disabled @endif>
                                        ✓ تطبيق
                                    </button>
                                    <button type="button" id="clearSelectionBtn"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        إلغاء التحديد
                                    </button>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('attendance.storeAll') }}" id="attendanceForm">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="department_id" value="{{ $departmentId }}">

                            {{-- Material Design Table --}}
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                                <table class="w-full">
                                    {{-- Header --}}
                                    <thead class="border-b border-gray-200 dark:border-gray-700">
                                        <tr>
                                            <th class="w-14 px-4 py-4">
                                                <input type="checkbox" id="selectAll" 
                                                    class="w-[18px] h-[18px] rounded-sm border-2 border-gray-400 text-blue-600 focus:ring-0 focus:ring-offset-0 cursor-pointer"
                                                    {{ $isLocked ? 'disabled' : '' }}>
                                            </th>
                                            <th class="px-4 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-400">
                                                الرقم الوظيفي
                                            </th>
                                            <th class="px-4 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-400">
                                                اسم الموظف
                                            </th>
                                            <th class="px-4 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-400">
                                                الحالة
                                            </th>
                                            <th class="px-4 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-400">
                                                ملاحظات
                                            </th>
                                        </tr>
                                    </thead>
                                    {{-- Body --}}
                                    <tbody id="employeesTableBody">
                                        @foreach ($employees as $index => $employee)
                                            @php
                                                $record = $employee->attendanceRecords->first();
                                                $statusColor = $statuses->firstWhere('code', $record?->status)?->color ?? '#9e9e9e';
                                            @endphp
                                            <tr class="employee-row border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors" 
                                                data-name="{{ $employee->name }}" 
                                                data-number="{{ $employee->employee_number }}">
                                                <input type="hidden" name="attendance[{{ $index }}][employee_id]" value="{{ $employee->id }}">

                                                {{-- Checkbox --}}
                                                <td class="px-4 py-3">
                                                    <input type="checkbox" 
                                                        class="employee-checkbox w-[18px] h-[18px] rounded-sm border-2 border-gray-400 text-blue-600 focus:ring-0 cursor-pointer"
                                                        {{ $isLocked ? 'disabled' : '' }}>
                                                </td>

                                                {{-- Employee Number --}}
                                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 font-mono">
                                                    {{ $employee->employee_number }}
                                                </td>

                                                {{-- Employee Name --}}
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-base font-medium"
                                                            style="background-color: {{ ['#1976d2', '#388e3c', '#f57c00', '#7b1fa2', '#c2185b', '#00796b'][($index % 6)] }}">
                                                            {{ mb_substr($employee->name, 0, 1) }}
                                                        </div>
                                                        <span class="text-sm text-gray-900 dark:text-gray-100">
                                                            {{ $employee->name }}
                                                        </span>
                                                    </div>
                                                </td>

                                                {{-- Status --}}
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-3 h-3 rounded-full flex-shrink-0 status-color"
                                                            style="background-color: {{ $statusColor }}"></span>
                                                        <select name="attendance[{{ $index }}][status]"
                                                            class="status-select text-sm bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md py-2 pe-8 ps-3 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer min-w-[130px] appearance-none"
                                                            style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: left 0.5rem center; background-size: 1rem;"
                                                            {{ $isLocked ? 'disabled' : '' }}
                                                            @if(isset($nonWorkingDayReason) && !empty($nonWorkingDayReason)) disabled @endif>
                                                            @if(isset($nonWorkingDayReason) && !empty($nonWorkingDayReason))
                                                                <option value="" selected>يوم عطلة</option>
                                                            @else
                                                                @foreach($statuses as $status)
                                                                    <option value="{{ $status->code }}" 
                                                                        data-color="{{ $status->color }}"
                                                                        {{ $record?->status == $status->code ? 'selected' : '' }}>
                                                                        {{ $status->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </td>

                                                {{-- Notes --}}
                                                <td class="px-4 py-3">
                                                    <input type="text" name="attendance[{{ $index }}][notes]"
                                                        value="{{ $record?->notes }}"
                                                        class="w-full text-sm bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md py-2 px-3 text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                        placeholder="ملاحظة..." 
                                                        {{ $isLocked ? 'disabled' : '' }}>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- عدد النتائج --}}
                            <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                                إجمالي الموظفين: <span id="totalCount">{{ $employees->count() }}</span> | 
                                نتائج البحث: <span id="visibleCount">{{ $employees->count() }}</span>
                            </div>

                            @if (!$isLocked)
                                @can('attendance.create')
                                <button type="submit"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                                    💾 حفظ الكل
                                </button>
                                @endcan
                            @endif
                        </form>
                    @elseif($departmentId)
                        <p class="text-gray-500 dark:text-gray-400">لا يوجد موظفين في هذا القسم</p>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">اختر القسم واضغط عرض</p>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const selectAll = document.getElementById('selectAll');
            const employeeRows = document.querySelectorAll('.employee-row');
            const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');
            const selectedCountEl = document.getElementById('selectedCount');
            const bulkCountEl = document.getElementById('bulkCount');
            const bulkActionBar = document.getElementById('bulkActionBar');
            const bulkStatus = document.getElementById('bulkStatus');
            const applyBulkBtn = document.getElementById('applyBulkBtn');
            const clearSelectionBtn = document.getElementById('clearSelectionBtn');
            const visibleCountEl = document.getElementById('visibleCount');

            // البحث الفوري
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    employeeRows.forEach(row => {
                        const name = row.dataset.name.toLowerCase();
                        const number = row.dataset.number.toLowerCase();
                        
                        if (name.includes(searchTerm) || number.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    visibleCountEl.textContent = visibleCount;
                });
            }

            // تحديد الكل
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    employeeCheckboxes.forEach(cb => {
                        const row = cb.closest('.employee-row');
                        if (row.style.display !== 'none') {
                            cb.checked = this.checked;
                        }
                    });
                    updateSelectedCount();
                });
            }

            // تحديث العداد عند تغيير أي checkbox
            employeeCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateSelectedCount);
            });

            // تحديث عداد المحددين
            function updateSelectedCount() {
                const checkedCount = document.querySelectorAll('.employee-checkbox:checked').length;
                selectedCountEl.textContent = checkedCount;
                bulkCountEl.textContent = checkedCount;
                
                if (checkedCount > 0) {
                    bulkActionBar.classList.remove('hidden');
                } else {
                    bulkActionBar.classList.add('hidden');
                }
            }

            // تطبيق الحالة على المحددين
// تطبيق الحالة على المحددين مع الحفظ التلقائي
if (applyBulkBtn) {
    applyBulkBtn.addEventListener('click', async function() {
        const selectedStatus = bulkStatus.value;
        const selectedColor = bulkStatus.options[bulkStatus.selectedIndex].dataset.color;
        const checkedBoxes = document.querySelectorAll('.employee-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            alert('الرجاء تحديد موظف واحد على الأقل');
            return;
        }

        if (!confirm(`هل تريد تطبيق وحفظ الحالة على ${checkedBoxes.length} موظف؟`)) {
            return;
        }

        // جمع employee_ids
        const employeeIds = [];
        checkedBoxes.forEach(cb => {
            const row = cb.closest('.employee-row');
            const employeeIdInput = row.querySelector('input[name*="[employee_id]"]');
            if (employeeIdInput) {
                employeeIds.push(employeeIdInput.value);
            }
        });

        // تعطيل الزر أثناء الحفظ
        applyBulkBtn.disabled = true;
        applyBulkBtn.textContent = '⏳ جاري الحفظ...';

        try {
            const response = await fetch('{{ route("attendance.ajax.bulk") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    date: '{{ $date }}',
                    department_id: '{{ $departmentId }}',
                    employee_ids: employeeIds,
                    status: selectedStatus
                })
            });

            const data = await response.json();

            if (data.success) {
                // تحديث الواجهة
                checkedBoxes.forEach(cb => {
                    const row = cb.closest('.employee-row');
                    const statusSelect = row.querySelector('.status-select');
                    const colorSpan = row.querySelector('span[class*="status-color"]');
                    
                    if (statusSelect) {
                        statusSelect.value = selectedStatus;
                    }
                    if (colorSpan) {
                        colorSpan.style.backgroundColor = selectedColor;
                    }
                });

                // رسالة نجاح
                showNotification(data.message, 'success');
                
                // إلغاء التحديد
                clearSelection();
            } else {
                showNotification(data.message || 'حدث خطأ', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('حدث خطأ في الاتصال', 'error');
        } finally {
            applyBulkBtn.disabled = false;
            applyBulkBtn.textContent = '✓ تطبيق';
        }
    });
}

// دالة إظهار الإشعارات
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-lg text-white font-bold z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transition = 'opacity 0.5s';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}
            // إلغاء التحديد
            if (clearSelectionBtn) {
                clearSelectionBtn.addEventListener('click', clearSelection);
            }

            function clearSelection() {
                employeeCheckboxes.forEach(cb => cb.checked = false);
                if (selectAll) selectAll.checked = false;
                updateSelectedCount();
            }

            // تحديث لون الحالة عند التغيير
// تحديث لون الحالة عند التغيير
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const row = this.closest('.employee-row');
        const selectedOption = this.options[this.selectedIndex];
        const color = selectedOption.dataset.color;
        const colorSpan = row.querySelector('.status-color');
        if (colorSpan) {
            colorSpan.style.backgroundColor = color;
        }
    });
});
        });
    </script>
</x-app-layout>
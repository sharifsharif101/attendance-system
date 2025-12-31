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
                                    <select id="bulkStatus" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm text-sm">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->code }}" data-color="{{ $status->color }}">
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="applyBulkBtn"
                                        class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm">
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

                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mb-4">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            <input type="checkbox" id="selectAll" 
                                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm dark:bg-gray-700"
                                                {{ $isLocked ? 'disabled' : '' }}>
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الرقم</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الاسم</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الحالة</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="employeesTableBody">
                                    @foreach ($employees as $index => $employee)
                                        @php
                                            $record = $employee->attendanceRecords->first();
                                        @endphp
                                        <tr class="employee-row" 
                                            data-name="{{ $employee->name }}" 
                                            data-number="{{ $employee->employee_number }}">
                                            <input type="hidden" name="attendance[{{ $index }}][employee_id]"
                                                value="{{ $employee->id }}">

                                            <td class="px-4 py-4 text-center">
             <input type="checkbox" class="employee-checkbox rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm dark:bg-gray-700"
    {{ $isLocked ? 'disabled' : '' }}>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $employee->employee_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $employee->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full status-color"
                                                        style="background-color: {{ $statuses->firstWhere('code', $record?->status)?->color ?? '#6b7280' }}"></span>
                                              <select name="attendance[{{ $index }}][status]"
    class="status-select rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm text-sm"
    {{ $isLocked ? 'disabled' : '' }}>
                                                        @foreach($statuses as $status)
                                                            <option value="{{ $status->code }}" 
                                                                data-color="{{ $status->color }}"
                                                                {{ $record?->status == $status->code ? 'selected' : '' }}>
                                                                {{ $status->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="text" name="attendance[{{ $index }}][notes]"
                                                    value="{{ $record?->notes }}"
                                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm text-sm w-full"
                                                    placeholder="ملاحظات..." {{ $isLocked ? 'disabled' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

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
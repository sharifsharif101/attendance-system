<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📋 سجل التدقيق
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- فلاتر البحث --}}
                    <form method="GET" action="{{ route('audit.index') }}" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                            {{-- المستخدم --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">المستخدم</label>
                                <select name="user_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm text-sm">
                                    <option value="">الكل</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- الإجراء --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الإجراء</label>
                                <select name="event" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm text-sm">
                                    <option value="">الكل</option>
                                    @foreach($events as $key => $value)
                                        <option value="{{ $key }}" {{ $event == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- النوع --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">النوع</label>
                                <select name="subject_type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm text-sm">
                                    <option value="">الكل</option>
                                    @foreach($subjectTypes as $key => $value)
                                        <option value="{{ $key }}" {{ $subjectType == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- من تاريخ --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">من تاريخ</label>
                                <input type="date" name="date_from" value="{{ $dateFrom }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm text-sm">
                            </div>

                            {{-- إلى تاريخ --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">إلى تاريخ</label>
                                <input type="date" name="date_to" value="{{ $dateTo }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm text-sm">
                            </div>

                            {{-- أزرار --}}
                            <div class="flex items-end gap-2">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    🔍 بحث
                                </button>
                                <a href="{{ route('audit.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    ↺ إعادة
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- عدد النتائج --}}
                    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                        إجمالي السجلات: {{ $activities->total() }}
                    </div>

                    {{-- جدول السجلات --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الإجراء</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الوصف</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">المستخدم</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">النوع</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">التاريخ</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">التفاصيل</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($activities as $activity)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        {{-- الإجراء --}}
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if($activity->event == 'created')
                                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded text-xs font-bold">
                                                    🟢 إنشاء
                                                </span>
                                            @elseif($activity->event == 'updated')
                                                <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 rounded text-xs font-bold">
                                                    🟡 تعديل
                                                </span>
                                            @elseif($activity->event == 'deleted')
                                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 rounded text-xs font-bold">
                                                    🔴 حذف
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300 rounded text-xs font-bold">
                                                    ⚪ {{ $activity->event }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- الوصف --}}
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $activity->description }}
                                        </td>

                                        {{-- المستخدم --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $activity->causer?->name ?? 'النظام' }}
                                        </td>

                                        {{-- النوع --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @php
                                                $typeLabels = [
                                                    'App\Models\AttendanceRecord' => 'سجل حضور',
                                                    'App\Models\Employee' => 'موظف',
                                                    'App\Models\Department' => 'قسم',
                                                    'App\Models\User' => 'مستخدم',
                                                    'App\Models\AttendanceStatus' => 'حالة حضور',
                                                ];
                                            @endphp
                                            {{ $typeLabels[$activity->subject_type] ?? class_basename($activity->subject_type) }}
                                        </td>

                                        {{-- التاريخ --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <div>{{ $activity->created_at->format('Y-m-d') }}</div>
                                            <div class="text-xs">{{ $activity->created_at->format('H:i:s') }}</div>
                                            <div class="text-xs text-blue-500">{{ $activity->created_at->diffForHumans() }}</div>
                                        </td>

                                        {{-- التفاصيل --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <button type="button" 
                                                onclick="showDetails({{ $activity->id }})"
                                                class="text-blue-500 hover:text-blue-700 text-sm font-bold">
                                                🔍 عرض
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            لا توجد سجلات
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- الترقيم --}}
                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal التفاصيل --}}
    <div id="detailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeModal()"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-auto p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">📋 تفاصيل السجل</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>
                
                <div id="detailsContent" class="text-gray-900 dark:text-gray-100">
                    <div class="text-center py-4">جاري التحميل...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        function showDetails(id) {
            document.getElementById('detailsModal').classList.remove('hidden');
            document.getElementById('detailsContent').innerHTML = '<div class="text-center py-4">جاري التحميل...</div>';
            
            fetch(`/audit/${id}`)
                .then(response => response.json())
                .then(data => {
                    let html = `
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">الإجراء:</span>
                                    <p class="font-bold">${getEventLabel(data.event)}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">المستخدم:</span>
                                    <p class="font-bold">${data.causer}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">النوع:</span>
                                    <p class="font-bold">${data.subject_type}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">التاريخ:</span>
                                    <p class="font-bold">${data.created_at}</p>
                                    <p class="text-xs text-blue-500">${data.time_ago}</p>
                                </div>
                            </div>
                            
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">الوصف:</span>
                                <p class="font-bold">${data.description}</p>
                            </div>
                    `;
                    
                    // التغييرات
                    if (data.old && data.attributes) {
                        html += `
                            <div class="mt-4">
                                <span class="text-sm text-gray-500 dark:text-gray-400 font-bold">التغييرات:</span>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-700 rounded-lg p-4 overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-right px-2 py-1 text-gray-500 dark:text-gray-400">الحقل</th>
                                                <th class="text-right px-2 py-1 text-red-500">القيمة القديمة</th>
                                                <th class="text-right px-2 py-1 text-green-500">القيمة الجديدة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        `;
                        
                        for (let key in data.attributes) {
                            if (data.old[key] !== data.attributes[key]) {
                                html += `
                                    <tr>
                                        <td class="px-2 py-1 font-bold">${key}</td>
                                        <td class="px-2 py-1 text-red-500">${data.old[key] ?? '-'}</td>
                                        <td class="px-2 py-1 text-green-500">${data.attributes[key] ?? '-'}</td>
                                    </tr>
                                `;
                            }
                        }
                        
                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    } else if (data.attributes) {
                        html += `
                            <div class="mt-4">
                                <span class="text-sm text-gray-500 dark:text-gray-400 font-bold">البيانات:</span>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <pre class="text-xs overflow-x-auto">${JSON.stringify(data.attributes, null, 2)}</pre>
                                </div>
                            </div>
                        `;
                    }
                    
                    html += '</div>';
                    document.getElementById('detailsContent').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('detailsContent').innerHTML = '<div class="text-center py-4 text-red-500">حدث خطأ في تحميل البيانات</div>';
                });
        }
        
        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }
        
        function getEventLabel(event) {
            const labels = {
                'created': '🟢 إنشاء',
                'updated': '🟡 تعديل',
                'deleted': '🔴 حذف'
            };
            return labels[event] || event;
        }
        
        // إغلاق بـ Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>
</x-app-layout>
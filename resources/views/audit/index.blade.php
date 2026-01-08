<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📋 سجل تغييرات النظام
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- فلاتر البحث --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                     <form method="GET" action="{{ route('audit.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">بحث بالاسم (موظف)</label>
                                <div class="flex gap-1">
                                    <input type="text" name="search_name" value="{{ $searchName ?? '' }}" 
                                        placeholder="اسم الموظف..."
                                        class="w-full rounded-r-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded-l-md text-sm transition duration-150 ease-in-out">
                                        بحث
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">المستخدم (من قام بالتعديل)</label>
                                <select name="user_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm text-sm" onchange="this.form.submit()">
                                    <option value="">الكل</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نوع الإجراء</label>
                                <select name="event" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm text-sm" onchange="this.form.submit()">
                                    <option value="">الكل</option>
                                    @foreach($events as $key => $value)
                                        <option value="{{ $key }}" {{ $event == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">من تاريخ</label>
                                <input type="date" name="date_from" value="{{ $dateFrom }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm text-sm" onchange="this.form.submit()">
                            </div>
                            <div class="flex items-end">
                                <a href="{{ route('audit.index') }}" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded text-sm border border-gray-300 transition">
                                    إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- الجدول المحسن --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="overflow-auto rounded-t-lg max-h-[75vh]">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 sticky top-0 z-10 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider whitespace-nowrap">من قام بالتعديل؟</th>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider whitespace-nowrap">ماذا حدث؟</th>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider whitespace-nowrap">تفاصيل التغييرات</th>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider whitespace-nowrap">متى؟</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($activities as $activity)
                                @php
                                    $eventColor = \App\Helpers\AuditHelper::getEventColor($activity->event);
                                    $subjectSummary = \App\Helpers\AuditHelper::getSubjectSummary($activity);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                    {{-- العمود الأول: المستخدم --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                                    {{ substr($activity->causer?->name ?? 'S', 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="mr-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                                    {{ $activity->causer?->name ?? 'النظام' }}
                                                    @if($activity->causer && method_exists($activity->causer, 'trashed') && $activity->causer->trashed())
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 mr-1">
                                                            المستخدم محذوف
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $activity->causer?->email ?? 'System' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- العمود الثاني: الحدث والملخص --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="w-2 h-2 rounded-full bg-{{ $eventColor }}-500"></span>
                                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                                {{ \App\Helpers\AuditHelper::getEventLabel($activity->event) }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                                            {{ $subjectSummary }}
                                        </div>
                                    </td>

                                    {{-- العمود الثالث: القيم --}}
                                    <td class="px-6 py-4">
                                        
                                        {{-- 1. حالة التعديل (Old -> New) --}}
                                        @if($activity->event == 'updated' && isset($activity->properties['old']) && isset($activity->properties['attributes']))
                                            <div class="space-y-1">
                                                @foreach($activity->properties['attributes'] as $key => $newValue)
                                                    @php
                                                        $oldValue = $activity->properties['old'][$key] ?? null;
                                                    @endphp
                                                    @if($oldValue != $newValue && !in_array($key, ['updated_at']))
                                                        <div class="flex items-center justify-between text-sm bg-gray-50 dark:bg-black/20 p-2 rounded border border-gray-100 dark:border-gray-700">
                                                            <div class="text-gray-500 text-xs font-bold w-1/3">
                                                                {{ \App\Helpers\AuditHelper::getFieldLabel($key) }}
                                                            </div>
                                                            <div class="flex-1 flex flex-col items-stretch justify-center gap-2 dir-ltr">
                                                                {{-- القيمة القديمة --}}
                                                                <div class="bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800 rounded p-2 text-center relative">
                                                                    <span class="absolute top-1 left-2 text-[10px] text-red-400">من</span>
                                                                    <span class="text-red-700 dark:text-red-400 line-through decoration-2 decoration-red-500/50 text-sm font-medium">
                                                                        {{ \App\Helpers\AuditHelper::formatValue($key, $oldValue) ?: '(فارغ)' }}
                                                                    </span>
                                                                </div>

                                                                {{-- سهم --}}
                                                                <div class="flex justify-center -my-1 z-10">
                                                                    <div class="bg-white dark:bg-gray-800 rounded-full p-1 border border-gray-100 dark:border-gray-700 shadow-sm">
                                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                                                        </svg>
                                                                    </div>
                                                                </div>

                                                                {{-- القيمة الجديدة --}}
                                                                <div class="bg-green-50 dark:bg-green-900/10 border border-green-100 dark:border-green-800 rounded p-2 text-center relative">
                                                                    <span class="absolute top-1 left-2 text-[10px] text-green-500">إلى</span>
                                                                    <span class="text-green-700 dark:text-green-400 font-bold text-sm">
                                                                        {{ \App\Helpers\AuditHelper::formatValue($key, $newValue) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        
                                        {{-- 2. حالة الإنشاء (Created) --}}
                                        @elseif($activity->event == 'created' && isset($activity->properties['attributes']))
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2">
                                                @foreach($activity->properties['attributes'] as $key => $value)
                                                    @if(!in_array($key, ['created_at', 'updated_at', 'id', 'employee_id', 'recorded_by']) && $value !== null)
                                                        <div class="flex items-center justify-between text-xs bg-green-50 dark:bg-green-900/20 p-2 rounded border border-green-100 dark:border-green-900/30">
                                                            <span class="text-gray-500 font-medium">{{ \App\Helpers\AuditHelper::getFieldLabel($key) }}</span>
                                                            <span class="font-bold text-gray-800 dark:text-gray-200 dir-ltr">
                                                                {{ \App\Helpers\AuditHelper::formatValue($key, $value) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>

                                        {{-- 3. حالة الحذف (Deleted) --}}
                                        @elseif($activity->event == 'deleted' && isset($activity->properties['old']))
                                             <div class="text-xs text-red-600 bg-red-50 p-2 rounded">
                                                تم حذف السجل الذي كان حالته: 
                                                <span class="font-bold">{{ \App\Helpers\AuditHelper::formatValue('status', $activity->properties['old']['status'] ?? '') }}</span>
                                             </div>
                                        @else
                                            <span class="text-gray-400 text-xs italic">لا توجد تفاصيل إضافية</span>
                                        @endif
                                    </td>

                                    {{-- العمود الرابع: التوقيت --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                            {{ $activity->created_at->format('Y-m-d') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dir-ltr">
                                            {{ $activity->created_at->format('h:i A') }}
                                        </div>
                                        <div class="text-xs text-blue-500 mt-1">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span class="text-lg font-medium">لا يوجد سجلات نشاط مطابقة</span>
                                            <span class="text-sm mt-1">حاول تخفيف الفلترة لرؤية المزيد</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    {{ $activities->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
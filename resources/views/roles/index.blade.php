@php
    // تعريف الألوان والتنسيقات للأدوار في مكان واحد (لم يتم تغيير هذا الجزء)
    $roleStyles = [
        'admin' => ['color' => 'red', 'label_color' => 'bg-red-500/10 text-red-600 dark:bg-red-500/20 dark:text-red-400', 'header_bg' => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300'],
        'manager' => ['color' => 'purple', 'label_color' => 'bg-purple-500/10 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400', 'header_bg' => 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300'],
        'general_supervisor' => ['color' => 'blue', 'label_color' => 'bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400', 'header_bg' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'],
        'supervisor' => ['color' => 'green', 'label_color' => 'bg-green-500/10 text-green-600 dark:bg-green-500/20 dark:text-green-400', 'header_bg' => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300'],
        'data_entry' => ['color' => 'yellow', 'label_color' => 'bg-yellow-500/10 text-yellow-600 dark:bg-yellow-500/20 dark:text-yellow-400', 'header_bg' => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'],
        'auditor' => ['color' => 'gray', 'label_color' => 'bg-gray-500/10 text-gray-600 dark:bg-gray-500/20 dark:text-gray-400', 'header_bg' => 'bg-gray-100 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center p-2">
            <h2 class="font-bold text-2xl text-gray-900 dark:text-gray-100 leading-tight flex items-center gap-2">
                <span class="text-blue-600 dark:text-blue-400">🔐</span> إدارة الأدوار والصلاحيات
            </h2>
            <a href="{{ route('roles.create') }}" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                <span class="ml-1">+</span> إضافة دور جديد
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <x-alert />

            {{-- 1. ملخص الأدوار (لم يتغير) --}}
            <div>
                <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                    📊 ملخص الأدوار الحالية
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    @foreach($roles as $role)
                        @php
                            $style = $roleStyles[$role->name] ?? ['color' => 'indigo', 'label_color' => 'bg-indigo-500/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400', 'header_bg' => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300'];
                            $color = $style['color'];
                        @endphp
                        {{-- بطاقة بتصميم Google Material Card --}}
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-between border-t-4 border-{{ $color }}-600">
                            <h4 class="font-extrabold text-lg text-gray-900 dark:text-gray-100 mb-2">
                                {{ $roleLabels[$role->name] ?? $role->name }}
                            </h4>
                            <div class="space-y-2 text-sm">
                                <span class="flex items-center gap-2 {{ $style['label_color'] }} px-3 py-1 rounded-full w-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2v-3m-4 5h-7a2 2 0 01-2-2v-4a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2z"></path></svg>
                                    <span class="font-medium">{{ $role->permissions->count() }} صلاحية</span>
                                </span>
                                <span class="flex items-center gap-2 bg-green-500/10 text-green-600 dark:bg-green-500/20 dark:text-green-400 px-3 py-1 rounded-full w-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span class="font-medium">{{ $role->users->count() }} مستخدم</span>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 2. مصفوفة الصلاحيات --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">📋 مصفوفة الأدوار والصلاحيات</h3>
                    
                    <div class="overflow-x-auto relative shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- رأس الجدول (Sticky Header) --}}
                            <thead class="sticky top-0 z-10">
                                <tr>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700 w-1/5 min-w-[200px] border-l dark:border-gray-700">
                                        الصلاحية
                                    </th>
                                    @foreach($roles as $role)
                                        @php
                                            $style = $roleStyles[$role->name] ?? ['color' => 'indigo', 'header_bg' => 'bg-gray-100 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300'];
                                            $color = $style['color'];
                                        @endphp
                                        <th class="px-3 py-4 text-center text-xs font-bold uppercase w-auto min-w-[120px] {{ $style['header_bg'] }} border-l dark:border-gray-700 last:border-l-0">
                                            <div class="font-extrabold text-sm mb-2">{{ $roleLabels[$role->name] ?? $role->name }}</div>
                                            <div class="flex flex-col gap-1 items-center">
                                                
                                                {{-- التعديل هنا: زر مخطط وواضح بشكل افتراضي --}}
                                                <a href="{{ route('roles.edit', $role) }}" 
                                                    class="w-full text-center px-2 py-1 rounded-md text-xs border border-{{ $color }}-600 text-{{ $color }}-600 dark:border-{{ $color }}-400 dark:text-{{ $color }}-400 hover:bg-{{ $color }}-600 hover:text-white transition shadow-sm">
                                                    ✏️ تعديل
                                                </a>
                                                
                                                @if($role->name !== 'admin')
                                                    <form method="POST" action="{{ route('roles.destroy', $role) }}" class="inline w-full" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        {{-- التعديل هنا: زر حذف بلون رمادي أغمق لتباين أفضل --}}
                                                        <button type="submit" class="w-full text-center px-2 py-1 bg-gray-600 text-white rounded-md text-xs hover:bg-red-700 transition shadow-sm">
                                                            🗑️ حذف
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            {{-- جسم الجدول (لم يتغير) --}}
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($groupedPermissions as $group => $perms)
                                    {{-- عنوان المجموعة --}}
                                    <tr class="bg-gray-100 dark:bg-gray-700/70">
                                        <td colspan="{{ $roles->count() + 1 }}" class="px-6 py-3">
                                            <span class="font-extrabold text-md text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                                @if($group === 'الحضور') 📅
                                                @elseif($group === 'التقارير') 📊
                                                @elseif($group === 'التدقيق') 🔍
                                                @elseif($group === 'الإدارة') ⚙️
                                                @endif
                                                {{ $group }}
                                            </span>
                                        </td>
                                    </tr>

                                    {{-- صفوف الصلاحيات --}}
                                    @foreach($perms as $permission)
                                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700 transition duration-150">
                                            <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 border-l dark:border-gray-700">
                                                {{ $permissionLabels[$permission->name] ?? $permission->name }}
                                            </td>
                                            @foreach($roles as $role)
                                                <td class="px-3 py-3 text-center border-l dark:border-gray-700 last:border-l-0">
                                                    @if($role->hasPermissionTo($permission->name))
                                                        {{-- تصميم Checkmark (Material Success) --}}
                                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-500 dark:bg-green-600 shadow-sm text-white transition duration-200 transform hover:scale-110" title="ممنوحة">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        </span>
                                                    @else
                                                        {{-- تصميم Cross (Material Alert/Disabled) --}}
                                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 text-red-500 dark:text-red-400 transition duration-200 transform hover:scale-110" title="غير ممنوحة">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. دليل الألوان (لم يتغير) --}}
                    <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border-t-2 border-gray-200 dark:border-gray-600">
                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-3 text-lg">📌 دليل الألوان</h4>
                        <div class="flex flex-wrap gap-4 text-sm">
                            @foreach($roleStyles as $roleName => $style)
                                @if(isset($style['color']))
                                    <span class="flex items-center gap-2 px-3 py-1 rounded-full {{ $style['label_color'] }} border border-{{ $style['color'] }}-300 dark:border-{{ $style['color'] }}-700 font-medium">
                                        <span class="w-3 h-3 rounded-full bg-{{ $style['color'] }}-600 shadow-md"></span>
                                        <span>{{ $roleLabels[$roleName] ?? $roleName }}</span>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
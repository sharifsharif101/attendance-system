@php
    // تم الاحتفاظ بمنطق الألوان كما هو لضمان عمل الكود الخلفي
    $roleStyles = [
        'admin' => ['color' => 'red', 'bg_soft' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-600 dark:text-red-400'],
        'manager' => ['color' => 'purple', 'bg_soft' => 'bg-purple-50 dark:bg-purple-900/20', 'text' => 'text-purple-600 dark:text-purple-400'],
        'general_supervisor' => ['color' => 'blue', 'bg_soft' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-600 dark:text-blue-400'],
        'supervisor' => ['color' => 'green', 'bg_soft' => 'bg-green-50 dark:bg-green-900/20', 'text' => 'text-green-600 dark:text-green-400'],
        'data_entry' => ['color' => 'yellow', 'bg_soft' => 'bg-yellow-50 dark:bg-yellow-900/20', 'text' => 'text-yellow-600 dark:text-yellow-400'],
        'auditor' => ['color' => 'gray', 'bg_soft' => 'bg-gray-50 dark:bg-gray-800', 'text' => 'text-gray-600 dark:text-gray-400'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end pb-2">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 dark:text-white tracking-tight leading-tight">
                    إدارة الأدوار
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium">التحكم في الصلاحيات والمستخدمين</p>
            </div>
            
            {{-- Apple Style Button: Pill shape, subtle shadow, System Blue --}}
            <a href="{{ route('roles.create') }}" 
                class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 border border-transparent rounded-full font-medium text-sm text-white shadow-lg shadow-blue-500/30 transition-all duration-200 transform hover:scale-[1.02]">
                <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                إضافة دور جديد
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 dark:bg-black min-h-screen">
        <div class="max-w-[85rem] mx-auto sm:px-6 lg:px-8 space-y-10">

            <x-alert />

            {{-- 1. ملخص الأدوار (Cards) --}}
            <section>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
                    @foreach($roles as $role)
                        @php
                            $style = $roleStyles[$role->name] ?? $roleStyles['auditor'];
                        @endphp
                        {{-- Card Design: Clean, No Borders, Soft Shadow, Rounded-3xl --}}
                        <div class="group relative bg-white dark:bg-gray-900 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none dark:border dark:border-white/10 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300">
                            <div class="flex flex-col h-full justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-2 h-2 rounded-full bg-{{ $style['color'] }}-500 ring-4 ring-{{ $style['color'] }}-100 dark:ring-{{ $style['color'] }}-900"></div>
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Role</span>
                                    </div>
                                    <h4 class="font-bold text-xl text-gray-900 dark:text-white mb-1">
                                        {{ $roleLabels[$role->name] ?? $role->name }}
                                    </h4>
                                </div>
                                
                                <div class="mt-6 space-y-3">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">الصلاحيات</span>
                                        <span class="font-semibold {{ $style['text'] }} {{ $style['bg_soft'] }} px-2.5 py-0.5 rounded-lg">
                                            {{ $role->permissions->count() }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">المستخدمين</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 rounded-lg">
                                            {{ $role->users->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 2. مصفوفة الصلاحيات --}}
            <section class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-[0_20px_50px_rgb(0,0,0,0.05)] dark:shadow-none dark:border dark:border-white/10 overflow-hidden ring-1 ring-black/5">
                <div class="p-8 pb-4">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">مصفوفة الصلاحيات</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">نظرة شاملة على توزيع الصلاحيات لكل دور</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        {{-- Sticky Header with Blur Effect --}}
                        <thead class="sticky top-0 z-20">
                            <tr>
                                <th class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b border-gray-100 dark:border-gray-800 text-right min-w-[220px]">
                                    الصلاحية
                                </th>
                                @foreach($roles as $role)
                                    @php $style = $roleStyles[$role->name] ?? $roleStyles['auditor']; @endphp
                                    <th class="px-4 py-5 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b border-gray-100 dark:border-gray-800 min-w-[140px]">
                                        <div class="flex flex-col items-center gap-3">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $roleLabels[$role->name] ?? $role->name }}</span>
                                            
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                {{-- Minimal Edit Button --}}
                                                <a href="{{ route('roles.edit', $role) }}" title="تعديل"
                                                    class="p-1.5 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </a>
                                                
                                                @if($role->name !== 'admin')
                                                    {{-- Minimal Delete Button --}}
                                                    <form method="POST" action="{{ route('roles.destroy', $role) }}" class="inline" onsubmit="return confirm('حذف الدور؟')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" title="حذف" class="p-1.5 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @foreach($groupedPermissions as $group => $perms)
                                {{-- Section Header: iOS Grouped List Style --}}
                                <tr class="bg-gray-50/50 dark:bg-white/5">
                                    <td colspan="{{ $roles->count() + 1 }}" class="px-8 py-3">
                                        <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                            {{ $group }}
                                        </span>
                                    </td>
                                </tr>

                                @foreach($perms as $permission)
                                    <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                                        <td class="px-8 py-4 text-sm font-medium text-gray-700 dark:text-gray-200">
                                            {{ $permissionLabels[$permission->name] ?? $permission->name }}
                                        </td>
                                        @foreach($roles as $role)
                                            <td class="px-4 py-4 text-center">
                                                @if($role->hasPermissionTo($permission->name))
                                                    {{-- Apple Style Checkmark: Simple, Blue, Clean --}}
                                                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 mx-auto">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                @else
                                                    {{-- Empty State: Subtle Dot --}}
                                                    <div class="inline-flex items-center justify-center w-8 h-8 mx-auto">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
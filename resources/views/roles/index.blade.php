<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                🔐 إدارة الأدوار والصلاحيات
            </h2>
            <a href="{{ route('roles.create') }}" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                + إضافة دور
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-alert />

            {{-- ملخص الأدوار --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">📊 ملخص الأدوار</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($roles as $role)
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border-r-4 
                            @if($role->name === 'admin') border-red-500
                            @elseif($role->name === 'manager') border-purple-500
                            @elseif($role->name === 'general_supervisor') border-blue-500
                            @elseif($role->name === 'supervisor') border-green-500
                            @elseif($role->name === 'data_entry') border-yellow-500
                            @elseif($role->name === 'auditor') border-gray-500
                            @else border-blue-400
                            @endif">
                            <h4 class="font-bold text-gray-800 dark:text-gray-200 text-sm">
                                {{ $roleLabels[$role->name] ?? $role->name }}
                            </h4>
                            <div class="flex justify-between mt-2 text-xs">
                                <span class="text-blue-500">{{ $role->permissions->count() }} صلاحية</span>
                                <span class="text-green-500">{{ $role->users->count() }} مستخدم</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- جدول الصلاحيات --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">📋 مصفوفة الصلاحيات</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700">
                                        الصلاحية
                                    </th>
                                    @foreach($roles as $role)
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase
                                            @if($role->name === 'admin') bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400
                                            @elseif($role->name === 'manager') bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400
                                            @elseif($role->name === 'general_supervisor') bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400
                                            @elseif($role->name === 'supervisor') bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400
                                            @elseif($role->name === 'data_entry') bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400
                                            @elseif($role->name === 'auditor') bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300
                                            @else bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300
                                            @endif">
                                            <div class="font-bold">{{ $roleLabels[$role->name] ?? $role->name }}</div>
                                            <div class="mt-2 flex justify-center gap-2">
                                                <a href="{{ route('roles.edit', $role) }}" 
                                                    class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-700 transition">
                                                    ✏️ تعديل
                                                </a>
                                                @if($role->name !== 'admin')
                                                    <form method="POST" action="{{ route('roles.destroy', $role) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-700 transition">
                                                            🗑️ حذف
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($groupedPermissions as $group => $perms)
                                    {{-- عنوان المجموعة --}}
                                    <tr class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800">
                                        <td colspan="{{ $roles->count() + 1 }}" class="px-6 py-3">
                                            <span class="font-bold text-gray-700 dark:text-gray-300 text-sm">
                                                @if($group === 'الحضور') 📅
                                                @elseif($group === 'التقارير') 📊
                                                @elseif($group === 'التدقيق') 🔍
                                                @elseif($group === 'الإدارة') ⚙️
                                                @endif
                                                {{ $group }}
                                            </span>
                                        </td>
                                    </tr>
                                    @foreach($perms as $permission)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $permissionLabels[$permission->name] ?? $permission->name }}
                                            </td>
                                            @foreach($roles as $role)
                                                <td class="px-4 py-3 text-center">
                                                    @if($role->hasPermissionTo($permission->name))
                                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30">
                                                            <span class="text-green-600 dark:text-green-400 text-lg">✓</span>
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30">
                                                            <span class="text-red-400 dark:text-red-500 text-lg">✗</span>
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

                    {{-- شرح الألوان --}}
                    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">📌 دليل الألوان</h4>
                        <div class="flex flex-wrap gap-4 text-sm">
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded bg-red-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">مدير النظام</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded bg-purple-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">مدير</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded bg-blue-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">مشرف عام</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded bg-green-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">مشرف قسم</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded bg-yellow-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">مدخل بيانات</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded bg-gray-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">مدقق</span>
                            </span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
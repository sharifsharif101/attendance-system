<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            تعديل الدور: {{ $role->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <x-alert />

                    <form method="POST" action="{{ route('roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اسم الدور (بالإنجليزية)</label>
                            <input type="text" name="name" value="{{ old('name', $role->name) }}" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm" 
                                {{ $role->name === 'admin' ? 'readonly' : '' }} required>
                            @if($role->name === 'admin')
                                <p class="text-xs text-yellow-500 mt-1">لا يمكن تغيير اسم دور مدير النظام</p>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">الصلاحيات</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($permissions as $permission)
                                    <label class="flex items-center p-2 border rounded dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm dark:bg-gray-700"
                                            {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                        <span class="mr-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $permissionLabels[$permission->name] ?? $permission->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                تحديث
                            </button>
                            <a href="{{ route('roles.index') }}" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                إلغاء
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
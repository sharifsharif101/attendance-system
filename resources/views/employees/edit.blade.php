<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            تعديل الموظف: {{ $employee->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <x-alert />

                    <form method="POST" action="{{ route('employees.update', $employee) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اسم الموظف</label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الرقم الوظيفي</label>
                            <input type="text" name="employee_number" value="{{ old('employee_number', $employee->employee_number) }}" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">القسم</label>
                            <select name="department_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm" required>
                                <option value="">اختر القسم</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" 
                                        {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" 
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm dark:bg-gray-700"
                                    {{ $employee->is_active ? 'checked' : '' }}>
                                <span class="mr-2 text-sm text-gray-700 dark:text-gray-300">مفعّل</span>
                            </label>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                تحديث
                            </button>
                            <a href="{{ route('employees.index') }}" 
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
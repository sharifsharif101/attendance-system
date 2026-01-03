<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            تعديل الحالة: {{ $status->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                   <x-alert />

                    <form method="POST" action="{{ route('statuses.update', $status) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اسم الحالة</label>
                            <input type="text" name="name" value="{{ old('name', $status->name) }}" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الرمز (بالإنجليزية)</label>
                            <input type="text" name="code" value="{{ old('code', $status->code) }}" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اللون</label>
                            <input type="color" name="color" value="{{ old('color', $status->color) }}" 
                                class="w-full h-10 rounded-md border-gray-300 dark:border-gray-600 shadow-sm">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الترتيب</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $status->sort_order) }}" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
                        </div>

<div class="mb-4 space-y-3">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نوع الحالة وتأثيرها على الحساب</label>
    
    @php
        $currentType = 'none';
        if (old('type')) {
            $currentType = old('type');
        } elseif ($status->counts_as_present) {
            $currentType = 'present';
        } elseif ($status->is_excluded) {
            $currentType = 'excluded';
        }
    @endphp

    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
        <input type="radio" name="type" value="present" 
            class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500 cursor-pointer"
            {{ $currentType == 'present' ? 'checked' : '' }}>
        <div class="mr-3">
            <span class="block text-sm font-medium text-gray-900 dark:text-gray-200">✅ حضور (Attendance)</span>
            <span class="block text-xs text-gray-500 dark:text-gray-400">يُحسب كحضور ويرفع نسبة الالتزام</span>
        </div>
    </label>

    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
        <input type="radio" name="type" value="excluded" 
            class="h-4 w-4 text-purple-600 border-gray-300 focus:ring-purple-500 cursor-pointer"
            {{ $currentType == 'excluded' ? 'checked' : '' }}>
        <div class="mr-3">
            <span class="block text-sm font-medium text-gray-900 dark:text-gray-200">⚪ استثناء (Exception)</span>
            <span class="block text-xs text-gray-500 dark:text-gray-400">لا يؤثر على النسبة (مثل: إجازة رسمية، إذن قانوني)</span>
        </div>
    </label>

    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
        <input type="radio" name="type" value="none" 
            class="h-4 w-4 text-gray-600 border-gray-300 focus:ring-gray-500 cursor-pointer"
            {{ $currentType == 'none' ? 'checked' : '' }}>
        <div class="mr-3">
            <span class="block text-sm font-medium text-gray-900 dark:text-gray-200">❌ غياب (Absence)</span>
            <span class="block text-xs text-gray-500 dark:text-gray-400">الوضع الافتراضي: يعتبر غياباً ويخفض النسبة</span>
        </div>
    </label>
</div>

<div class="mb-4">
    <label class="flex items-center">
        <input type="checkbox" name="is_active" value="1" 
            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm dark:bg-gray-700"
            {{ old('is_active', $status->is_active) ? 'checked' : '' }}>
        <span class="mr-2 text-sm text-gray-700 dark:text-gray-300">مفعّل</span>
    </label>
</div>

                        <div class="flex gap-2">
                            <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                تحديث
                            </button>
                            <a href="{{ route('statuses.index') }}" 
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
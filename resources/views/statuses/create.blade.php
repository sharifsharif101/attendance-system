<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            إضافة حالة جديدة
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('statuses.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم الحالة</label>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                class="w-full rounded-md border-gray-300 shadow-sm" 
                                placeholder="مثال: حاضر" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">الرمز (بالإنجليزية)</label>
                            <input type="text" name="code" value="{{ old('code') }}" 
                                class="w-full rounded-md border-gray-300 shadow-sm" 
                                placeholder="مثال: present" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">اللون</label>
                            <input type="color" name="color" value="{{ old('color', '#6b7280') }}" 
                                class="w-full h-10 rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">الترتيب</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" 
                                    class="rounded border-gray-300 text-blue-600 shadow-sm" checked>
                                <span class="mr-2 text-sm text-gray-700">مفعّل</span>
                            </label>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                حفظ
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
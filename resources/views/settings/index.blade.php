<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            إعدادات النظام
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
         <x-alert />

            {{-- الإعدادات الافتراضية --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">الإعدادات الافتراضية</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">هذه الإعدادات تُطبق على الأشهر التي ليس لها إعداد خاص</p>
                    
                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 flex justify-between items-center">
                        <div>
                            <h4 class="font-bold text-blue-700 dark:text-blue-300">🎉 العطلات الرسمية</h4>
                            <p class="text-sm text-blue-600 dark:text-blue-400">يمكنك إدارة الأعياد والمناسبات الوطنية التي يتم استثناؤها تلقائياً.</p>
                        </div>
                        <a href="{{ route('holidays.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                             إدارة العطلات
                        </a>
                    </div>
                    <form method="POST" action="{{ route('settings.update') }}">
                        @csrf
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach($days as $key => $name)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                        name="weekend_days[]" 
                                        value="{{ $key }}"
                                        {{ in_array($key, $defaultWeekendDays) ? 'checked' : '' }}
                                        class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm dark:bg-gray-700">
                                    <span class="mr-2 text-gray-700 dark:text-gray-300">{{ $name }}</span>
                                </label>
                            @endforeach
                        </div>
                        
                        <div class="mb-4 max-w-xs">
                            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">مدة تحديث رمز QR (بالثواني)</label>
                            <input type="number" 
                                name="qr_refresh_seconds" 
                                value="{{ $qrRefreshSeconds }}" 
                                min="10" 
                                step="5"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                        </div>
                        <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            حفظ الإعدادات الافتراضية
                        </button>
                    </form>
                </div>
            </div>

            {{-- إعدادات شهر معين --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">إعدادات شهر معين</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">يمكنك تخصيص أيام الإجازة لشهر محدد</p>
                    
                    {{-- اختيار الشهر --}}
                    <form method="GET" action="{{ route('settings.index') }}" class="mb-6">
                        <div class="flex gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اختر الشهر</label>
                                <input type="month" name="month" value="{{ $month }}" 
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
                            </div>
                            <button type="submit" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                عرض
                            </button>
                        </div>
                    </form>

                    {{-- حالة الشهر --}}
                    @if($hasCustomSetting)
                        <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded mb-4">
                            ⚙️ هذا الشهر له إعدادات خاصة
                        </div>
                    @else
                        <div class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 px-4 py-3 rounded mb-4">
                            📋 هذا الشهر يستخدم الإعدادات الافتراضية
                        </div>
                    @endif

                    {{-- نموذج إعدادات الشهر --}}
                    <form method="POST" action="{{ route('settings.updateMonth') }}">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach($days as $key => $name)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                        name="weekend_days[]" 
                                        value="{{ $key }}"
                                        {{ in_array($key, $weekendDays) ? 'checked' : '' }}
                                        class="rounded border-gray-300 dark:border-gray-600 text-green-600 shadow-sm dark:bg-gray-700">
                                    <span class="mr-2 text-gray-700 dark:text-gray-300">{{ $name }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" 
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                حفظ إعدادات {{ $month }}
                            </button>

                            @if($hasCustomSetting)
                                </form>
                                <form method="POST" action="{{ route('settings.resetMonth') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <button type="submit" 
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        onclick="return confirm('هل تريد إعادة هذا الشهر للإعدادات الافتراضية؟')">
                                        إعادة للافتراضي
                                    </button>
                                </form>
                            @else
                        </div>
                    </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
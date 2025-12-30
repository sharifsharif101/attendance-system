<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            إعدادات النظام
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- الإعدادات الافتراضية --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">الإعدادات الافتراضية</h3>
                    <p class="text-sm text-gray-500 mb-4">هذه الإعدادات تُطبق على الأشهر التي ليس لها إعداد خاص</p>
                    
                    <form method="POST" action="{{ route('settings.update') }}">
                        @csrf
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach($days as $key => $name)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                        name="weekend_days[]" 
                                        value="{{ $key }}"
                                        {{ in_array($key, $defaultWeekendDays) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <span class="mr-2 text-gray-700">{{ $name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            حفظ الإعدادات الافتراضية
                        </button>
                    </form>
                </div>
            </div>

            {{-- إعدادات شهر معين --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">إعدادات شهر معين</h3>
                    <p class="text-sm text-gray-500 mb-4">يمكنك تخصيص أيام الإجازة لشهر محدد</p>
                    
                    {{-- اختيار الشهر --}}
                    <form method="GET" action="{{ route('settings.index') }}" class="mb-6">
                        <div class="flex gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">اختر الشهر</label>
                                <input type="month" name="month" value="{{ $month }}" 
                                    class="rounded-md border-gray-300 shadow-sm">
                            </div>
                            <button type="submit" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                عرض
                            </button>
                        </div>
                    </form>

                    {{-- حالة الشهر --}}
                    @if($hasCustomSetting)
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                            ⚙️ هذا الشهر له إعدادات خاصة
                        </div>
                    @else
                        <div class="bg-gray-100 border border-gray-300 text-gray-600 px-4 py-3 rounded mb-4">
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
                                        class="rounded border-gray-300 text-green-600 shadow-sm">
                                    <span class="mr-2 text-gray-700">{{ $name }}</span>
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
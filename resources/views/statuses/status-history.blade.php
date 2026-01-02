<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📋 تاريخ تغييرات حالة: 
                <span class="px-2 py-1 rounded text-white" style="background-color: {{ $status->color }}">
                    {{ $status->name }}
                </span>
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('statuses.history') }}" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                    عرض الكل
                </a>
                <a href="{{ route('statuses.index') }}" 
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                    ← العودة للحالات
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- معلومات الحالة الحالية -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">الإعدادات الحالية</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-sm text-gray-500 dark:text-gray-400">الكود</div>
                            <div class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $status->code }}</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-sm text-gray-500 dark:text-gray-400">يُحسب كحضور</div>
                            <div class="text-2xl">{{ $status->counts_as_present ? '✅' : '❌' }}</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-sm text-gray-500 dark:text-gray-400">مستثنى</div>
                            <div class="text-2xl">{{ $status->is_excluded ? '⚪' : '❌' }}</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-sm text-gray-500 dark:text-gray-400">الحالة</div>
                            <div class="text-lg">
                                @if($status->is_active)
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded text-sm">مفعّل</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 rounded text-sm">معطّل</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- سجل التغييرات -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">سجل التغييرات</h3>

                    @if($history->isEmpty())
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📭</div>
                            <p class="text-gray-500 dark:text-gray-400 text-lg">لا توجد تغييرات مسجلة لهذه الحالة</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">ستظهر التغييرات هنا عند تعديل إعدادات هذه الحالة</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($history as $record)
                                <div class="border dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="space-y-2">
                                            @if($record->old_is_excluded !== $record->new_is_excluded)
                                                <div class="flex items-center gap-2 text-sm">
                                                    <span class="text-gray-500 dark:text-gray-400 w-28">مستثنى:</span>
                                                    <span class="px-2 py-0.5 rounded {{ $record->old_is_excluded ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                                        {{ $record->old_is_excluded ? 'نعم' : 'لا' }}
                                                    </span>
                                                    <span class="text-gray-400">→</span>
                                                    <span class="px-2 py-0.5 rounded {{ $record->new_is_excluded ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                                        {{ $record->new_is_excluded ? 'نعم' : 'لا' }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if($record->old_counts_as_present !== $record->new_counts_as_present)
                                                <div class="flex items-center gap-2 text-sm">
                                                    <span class="text-gray-500 dark:text-gray-400 w-28">يُحسب كحضور:</span>
                                                    <span class="px-2 py-0.5 rounded {{ $record->old_counts_as_present ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                                        {{ $record->old_counts_as_present ? 'نعم' : 'لا' }}
                                                    </span>
                                                    <span class="text-gray-400">→</span>
                                                    <span class="px-2 py-0.5 rounded {{ $record->new_counts_as_present ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                                        {{ $record->new_counts_as_present ? 'نعم' : 'لا' }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-left text-sm text-gray-500 dark:text-gray-400">
                                            <div>{{ $record->created_at->format('Y-m-d H:i') }}</div>
                                            <div class="text-xs">بواسطة: {{ $record->changedByUser?->name ?? 'غير معروف' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $history->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

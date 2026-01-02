<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📋 تاريخ تغييرات حالات الحضور
            </h2>
            <a href="{{ route('statuses.index') }}" 
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                ← العودة للحالات
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if($history->isEmpty())
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📭</div>
                            <p class="text-gray-500 dark:text-gray-400 text-lg">لا توجد تغييرات مسجلة بعد</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">ستظهر التغييرات هنا عند تعديل إعدادات أي حالة</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">التاريخ</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">الحالة</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">التغيير</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">بواسطة</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($history as $record)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <div>{{ $record->created_at->format('Y-m-d') }}</div>
                                                <div class="text-xs">{{ $record->created_at->format('H:i') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->attendanceStatus)
                                                    <span class="px-2 py-1 rounded text-white text-sm" style="background-color: {{ $record->attendanceStatus->color }}">
                                                        {{ $record->attendanceStatus->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">{{ $record->status_code }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <div class="space-y-1">
                                                    @if($record->old_is_excluded !== $record->new_is_excluded)
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-500 dark:text-gray-400">مستثنى:</span>
                                                            <span class="px-2 py-0.5 rounded text-xs {{ $record->old_is_excluded ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                                                {{ $record->old_is_excluded ? 'نعم' : 'لا' }}
                                                            </span>
                                                            <span class="text-gray-400">←</span>
                                                            <span class="px-2 py-0.5 rounded text-xs {{ $record->new_is_excluded ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                                                {{ $record->new_is_excluded ? 'نعم' : 'لا' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if($record->old_counts_as_present !== $record->new_counts_as_present)
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-500 dark:text-gray-400">يُحسب كحضور:</span>
                                                            <span class="px-2 py-0.5 rounded text-xs {{ $record->old_counts_as_present ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                                                {{ $record->old_counts_as_present ? 'نعم' : 'لا' }}
                                                            </span>
                                                            <span class="text-gray-400">←</span>
                                                            <span class="px-2 py-0.5 rounded text-xs {{ $record->new_counts_as_present ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                                                {{ $record->new_counts_as_present ? 'نعم' : 'لا' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $record->changedByUser?->name ?? 'غير معروف' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

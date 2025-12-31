<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            سجل التدقيق
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">المستخدم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">العملية</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">التفاصيل</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($activities as $activity)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $activity->created_at->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $activity->causer?->name ?? 'النظام' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($activity->description == 'created')
                                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded">إنشاء</span>
                                        @elseif($activity->description == 'updated')
                                            <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 rounded">تعديل</span>
                                        @elseif($activity->description == 'deleted')
                                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 rounded">حذف</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded">{{ $activity->description }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        @php
                                            $properties = $activity->properties;
                                        @endphp
                                        
                                        @if($activity->description == 'updated' && isset($properties['old']) && isset($properties['attributes']))
                                            @foreach($properties['attributes'] as $key => $newValue)
                                                @if($key != 'updated_at' && isset($properties['old'][$key]))
                                                    @php
                                                        $oldValue = $properties['old'][$key];
                                                        $statusLabels = [
                                                            'present' => 'حاضر',
                                                            'absent' => 'غائب',
                                                            'late' => 'متأخر',
                                                            'excused' => 'مأذون',
                                                            'leave' => 'إجازة',
                                                        ];
                                                        if($key == 'status') {
                                                            $oldValue = $statusLabels[$oldValue] ?? $oldValue;
                                                            $newValue = $statusLabels[$newValue] ?? $newValue;
                                                        }
                                                    @endphp
                                                    <div>
                                                        <span class="text-red-500">{{ $oldValue }}</span>
                                                        ←
                                                        <span class="text-green-500">{{ $newValue }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @elseif($activity->description == 'created' && isset($properties['attributes']))
                                            @php
                                                $attrs = $properties['attributes'];
                                                $statusLabels = [
                                                    'present' => 'حاضر',
                                                    'absent' => 'غائب',
                                                    'late' => 'متأخر',
                                                    'excused' => 'مأذون',
                                                    'leave' => 'إجازة',
                                                ];
                                            @endphp
                                            <span class="text-green-500">
                                                {{ $statusLabels[$attrs['status']] ?? $attrs['status'] ?? '' }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        لا توجد سجلات
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
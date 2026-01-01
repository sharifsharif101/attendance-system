<x-app-layout>
    <!-- الخلفية العامة: رمادي فاتح جداً شبيه بـ iOS/macOS -->
    <div class="min-h-screen bg-[#F5F5F7] dark:bg-[#1c1c1e] font-sans antialiased text-gray-900 dark:text-white transition-colors duration-300">
        
        <div class="flex flex-col lg:flex-row max-w-[1600px] mx-auto">
            
            {{-- المحتوى الرئيسي --}}
            <div class="flex-1 p-6 lg:p-10">
                
                {{-- الهيدر: بسيط ونظيف --}}
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-1">
                            {{ now()->translatedFormat('l، d F') }}
                        </p>
                        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                            مرحباً، {{ Auth::user()->name }}
                        </h1>
                    </div>
                    
                    {{-- زر الإجراءات السريعة (تصميم زر Apple) --}}
                    <div>
                        <button class="bg-gray-900 dark:bg-white dark:text-black text-white px-5 py-2.5 rounded-full text-sm font-medium hover:scale-105 transition-transform duration-200 shadow-lg shadow-gray-200 dark:shadow-none flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>إجراء جديد</span>
                        </button>
                    </div>
                </div>

                {{-- شبكة البانر والإحصائيات (Grid Layout) --}}
                <div class="grid grid-cols-12 gap-6 mb-10">
                    
                    {{-- البانر الرئيسي: تصميم "Feature Card" --}}
                    <div class="col-span-12 lg:col-span-8 relative overflow-hidden bg-gray-900 dark:bg-black rounded-[2.5rem] p-8 text-white shadow-2xl shadow-gray-200 dark:shadow-none flex flex-col justify-between h-64 group">
                        <!-- تأثيرات خلفية ناعمة -->
                        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/30 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/2 group-hover:bg-blue-600/40 transition duration-700"></div>
                        <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-600/30 rounded-full blur-[60px] translate-y-1/2 -translate-x-1/2"></div>
                        
                        <div class="relative z-10">
                            <span class="px-3 py-1 rounded-full border border-white/20 text-xs font-medium bg-white/10 backdrop-blur-md">نظام الحضور</span>
                            <h2 class="mt-4 text-3xl font-bold leading-tight">إدارة سلسة <br>لفريق العمل.</h2>
                        </div>
                        
                        <div class="relative z-10">
                            <a href="{{ route('attendance.index') }}" 
                               class="inline-flex items-center gap-2 bg-white text-black px-6 py-3 rounded-full text-sm font-bold hover:bg-gray-100 transition shadow-lg">
                                تسجيل الحضور
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    {{-- بطاقة نسبة الحضور (Bento Box Style) --}}
                    <div class="col-span-12 lg:col-span-4 bg-white dark:bg-[#2c2c2e] rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-800 flex flex-col justify-center items-center relative overflow-hidden">
                        <div class="relative z-10 text-center">
                            <div class="w-24 h-24 mx-auto mb-4 relative flex items-center justify-center">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-gray-100 dark:text-gray-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
                                    <path class="{{ $todayAttendanceRate >= 80 ? 'text-green-500' : ($todayAttendanceRate >= 50 ? 'text-yellow-500' : 'text-red-500') }}" 
                                          stroke-dasharray="{{ $todayAttendanceRate }}, 100" 
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                          fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                                </svg>
                                <span class="absolute text-2xl font-bold">{{ $todayAttendanceRate }}%</span>
                            </div>
                            <h3 class="text-gray-500 dark:text-gray-400 font-medium">نسبة الحضور اليوم</h3>
                        </div>
                    </div>
                </div>

                {{-- الإحصائيات السريعة (Cards) --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-10">
                    {{-- بطاقة 1 --}}
                    <div class="bg-white dark:bg-[#2c2c2e] p-6 rounded-3xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-lg transition-shadow duration-300 border border-gray-100 dark:border-gray-800">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">إجمالي الموظفين</p>
                                <p class="text-3xl font-bold tracking-tight">{{ $totalEmployees }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    {{-- بطاقة 2 --}}
                    <div class="bg-white dark:bg-[#2c2c2e] p-6 rounded-3xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-lg transition-shadow duration-300 border border-gray-100 dark:border-gray-800">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">الأقسام</p>
                                <p class="text-3xl font-bold tracking-tight">{{ $totalDepartments }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-green-50 dark:bg-green-500/10 flex items-center justify-center text-green-600 dark:text-green-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        </div>
                    </div>

                    {{-- بطاقة 3 --}}
                    <div class="bg-white dark:bg-[#2c2c2e] p-6 rounded-3xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-lg transition-shadow duration-300 border border-gray-100 dark:border-gray-800">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">المستخدمين</p>
                                <p class="text-3xl font-bold tracking-tight">{{ $totalUsers }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- الحالات اليومية (Pills Style) --}}
                <div class="mb-10">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 px-1">إحصائيات اليوم</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($statuses as $status)
                            @php $count = $todayStats[$status->code] ?? 0; @endphp
                            <div class="flex-1 min-w-[140px] bg-white dark:bg-[#2c2c2e] rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm hover:scale-105 transition-transform cursor-default">
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-8 rounded-full" style="background-color: {{ $status->color }}"></span>
                                    <div>
                                        <span class="block text-2xl font-bold">{{ $count }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $status->name }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- جدول الأقسام (Mac Finder Style List) --}}
                <div class="bg-white dark:bg-[#2c2c2e] rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between mb-6 px-2">
                        <h3 class="text-lg font-bold">أداء الأقسام</h3>
                        <a href="{{ route('departments.index') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 transition">عرض الكل</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                    <th class="pb-4 pr-4">القسم</th>
                                    <th class="pb-4">الفريق</th>
                                    <th class="pb-4">النسبة</th>
                                    <th class="pb-4 pl-4">التقييم</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                @foreach($departmentStats->take(5) as $dept)
                                    <tr class="group hover:bg-gray-50 dark:hover:bg-white/5 transition duration-200">
                                        <td class="py-4 pr-4 rounded-r-xl">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold shadow-inner">
                                                    {{ mb_substr($dept->name, 0, 1) }}
                                                </div>
                                                <span class="font-semibold text-gray-900 dark:text-white">{{ $dept->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 text-gray-500 font-medium">{{ $dept->total_employees }} موظف</td>
                                        <td class="py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-24 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $dept->attendance_rate >= 80 ? 'bg-green-500' : ($dept->attendance_rate >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                        style="width: {{ $dept->attendance_rate }}%"></div>
                                                </div>
                                                <span class="text-sm font-bold">{{ $dept->attendance_rate }}%</span>
                                            </div>
                                        </td>
                                        <td class="py-4 pl-4 rounded-l-xl">
                                            @if($dept->attendance_rate >= 80)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">ممتاز</span>
                                            @elseif($dept->attendance_rate >= 50)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">متوسط</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">منخفض</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- الشريط الجانبي الأيمن: Glassy Sidebar --}}
            <div class="w-full lg:w-96 bg-white/60 dark:bg-[#1c1c1e]/60 backdrop-blur-xl border-t lg:border-t-0 lg:border-r border-gray-200 dark:border-gray-800 p-8 flex flex-col gap-8">
                
                {{-- بروفايل المستخدم --}}
                <div class="text-center">
                    <div class="relative inline-block group">
                        <div class="w-24 h-24 mx-auto bg-gradient-to-tr from-gray-200 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-full p-1 shadow-lg">
                            <div class="w-full h-full rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-3xl font-bold text-gray-700 dark:text-gray-200">
                                {{ mb_substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="absolute bottom-1 right-1 w-6 h-6 bg-green-500 border-4 border-white dark:border-[#1c1c1e] rounded-full"></div>
                    </div>
                    <h4 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</h4>
                    <p class="text-sm text-gray-500">{{ Auth::user()->roles->first()->name ?? 'مستخدم النظام' }}</p>
                </div>

                {{-- التنبيهات (iOS Notifications Style) --}}
          {{-- التحقق من وجود أي تنبيهات --}}
@if($expiredDocuments > 0 || $expiringPassports > 0 || $expiringResidencies > 0 || $expiringContracts > 0)
    
    <div class="mb-8">
        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 px-2">تنبيهات عاجلة</h4>
        
        {{-- الحاوية الرئيسية: تأثير زجاجي مع حدود ملونة خفيفة وظل بارز --}}
        <div class="relative overflow-hidden bg-white dark:bg-[#2c2c2e] rounded-[2rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-red-100 dark:border-red-900/30">
            
            {{-- خلفية جمالية (Glow Effect) --}}
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-red-500/10 rounded-full blur-[60px] pointer-events-none"></div>
            
            <div class="relative z-10">
                {{-- رأس البطاقة --}}
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">إجراءات تتطلب انتباهك</h3>
                    </div>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-white/10 rounded-full text-xs font-bold text-gray-600 dark:text-gray-300">
                        {{ $expiredDocuments + $expiringPassports + $expiringResidencies + $expiringContracts }} تنبيهات
                    </span>
                </div>

                {{-- شبكة التنبيهات (Grid) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    
                    {{-- 1. الوثائق المنتهية (الأخطر - لون أحمر) --}}
                    @if($expiredDocuments > 0)
                    <a href="{{ route('documents.expiring', ['type' => 'expired']) }}" 
                       class="group flex items-center gap-4 p-4 bg-red-50 dark:bg-red-900/10 rounded-2xl hover:bg-red-100 dark:hover:bg-red-900/20 transition-all duration-300 border border-transparent hover:border-red-200">
                        <div class="w-12 h-12 bg-white dark:bg-red-900/40 rounded-xl flex items-center justify-center shadow-sm text-2xl group-hover:scale-110 transition-transform">
                            🚨
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400 leading-none mb-1">{{ $expiredDocuments }}</p>
                            <p class="text-xs font-semibold text-red-600/70 dark:text-red-400/70">وثائق منتهية بالفعل</p>
                        </div>
                    </a>
                    @endif

                    {{-- 2. العقود (أزرق) --}}
                    @if($expiringContracts > 0)
                    <a href="{{ route('documents.expiring', ['type' => 'contract']) }}" 
                       class="group flex items-center gap-4 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-2xl hover:bg-blue-100 dark:hover:bg-blue-900/20 transition-all duration-300 border border-transparent hover:border-blue-200">
                        <div class="w-12 h-12 bg-white dark:bg-blue-900/40 rounded-xl flex items-center justify-center shadow-sm text-blue-500 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 leading-none mb-1">{{ $expiringContracts }}</p>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">عقود تنتهي قريباً</p>
                        </div>
                    </a>
                    @endif

                    {{-- 3. الجوازات (أصفر/برتقالي) --}}
                    @if($expiringPassports > 0)
                    <a href="{{ route('documents.expiring', ['type' => 'passport']) }}" 
                       class="group flex items-center gap-4 p-4 bg-amber-50 dark:bg-amber-900/10 rounded-2xl hover:bg-amber-100 dark:hover:bg-amber-900/20 transition-all duration-300 border border-transparent hover:border-amber-200">
                        <div class="w-12 h-12 bg-white dark:bg-amber-900/40 rounded-xl flex items-center justify-center shadow-sm text-amber-500 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 leading-none mb-1">{{ $expiringPassports }}</p>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">جوازات سفر</p>
                        </div>
                    </a>
                    @endif

                    {{-- 4. الإقامات (زمردي/أخضر) --}}
                    @if($expiringResidencies > 0)
                    <a href="{{ route('documents.expiring', ['type' => 'residency']) }}" 
                       class="group flex items-center gap-4 p-4 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl hover:bg-emerald-100 dark:hover:bg-emerald-900/20 transition-all duration-300 border border-transparent hover:border-emerald-200">
                        <div class="w-12 h-12 bg-white dark:bg-emerald-900/40 rounded-xl flex items-center justify-center shadow-sm text-emerald-500 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 leading-none mb-1">{{ $expiringResidencies }}</p>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">إقامات وهوية</p>
                        </div>
                    </a>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
@endif

                {{-- آخر الأنشطة (Timeline Style) --}}
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-4 px-2">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">آخر السجلات</h4>
                        <a href="{{ route('audit.index') }}" class="text-xs text-blue-500 hover:text-blue-600 font-medium">مشاهدة الكل</a>
                    </div>
                    <div class="relative pl-4 space-y-6 before:absolute before:right-[1.15rem] before:top-2 before:bottom-2 before:w-px before:bg-gray-200 dark:before:bg-gray-800">
                        @forelse($recentRecords->take(4) as $record)
                            @php $statusData = $statuses->firstWhere('code', $record->status); @endphp
                            <div class="relative pr-8">
                                <div class="absolute right-0 top-1 w-2.5 h-2.5 rounded-full ring-4 ring-white dark:ring-[#1c1c1e]" style="background-color: {{ $statusData->color ?? '#9ca3af' }}"></div>
                                <div class="bg-white dark:bg-[#2c2c2e] p-3 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                                    <div class="flex justify-between items-start mb-1">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $record->employee->name ?? 'غير معروف' }}</p>
                                        <span class="text-[10px] text-gray-400">{{ $record->created_at->format('H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $statusData->name ?? $record->status }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-400 py-4">لا توجد سجلات حديثة</p>
                        @endforelse
                    </div>
                </div>

                {{-- الوصول السريع (Quick Actions Grid) --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 px-2">القائمة السريعة</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('attendance.index') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-white/5 rounded-2xl hover:bg-gray-100 dark:hover:bg-white/10 transition group">
                            <span class="text-2xl mb-2 group-hover:scale-110 transition-transform">📅</span>
                            <span class="text-xs font-medium">الحضور</span>
                        </a>
                        <a href="{{ route('employees.index') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-white/5 rounded-2xl hover:bg-gray-100 dark:hover:bg-white/10 transition group">
                            <span class="text-2xl mb-2 group-hover:scale-110 transition-transform">👥</span>
                            <span class="text-xs font-medium">الموظفين</span>
                        </a>
                        <a href="{{ route('reports.monthly') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-white/5 rounded-2xl hover:bg-gray-100 dark:hover:bg-white/10 transition group">
                            <span class="text-2xl mb-2 group-hover:scale-110 transition-transform">📊</span>
                            <span class="text-xs font-medium">التقارير</span>
                        </a>
                        <a href="{{ route('settings.index') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-white/5 rounded-2xl hover:bg-gray-100 dark:hover:bg-white/10 transition group">
                            <span class="text-2xl mb-2 group-hover:scale-110 transition-transform">⚙️</span>
                            <span class="text-xs font-medium">الإعدادات</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
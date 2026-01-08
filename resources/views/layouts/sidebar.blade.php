<div class="flex flex-col w-64 h-screen bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 transition-colors duration-300">
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="block h-8 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
            <span class="text-xl font-extrabold text-gray-900 dark:text-white tracking-wide">Nexus</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6">

        <!-- Section: General -->
        <div>
            <div class="px-2 mb-2 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                عام
            </div>
            <div class="space-y-1">
            <div class="space-y-1 font-bold">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('dashboard') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                   </svg>
                   <span>لوحة التحكم</span>
                </a>
            </div>
        </div>

        <!-- Section: Management -->
        <div>
            <div class="px-2 mb-2 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                الإدارة
            </div>
            <div class="space-y-1">
                @can('attendance.view')
                <a href="{{ route('attendance.index') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('attendance.*') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                   </svg>
                   <span>الحضور</span>
                </a>
                @endcan

                @can('attendance.create')
                <a href="{{ route('qr.display') }}" target="_blank"
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('qr.*') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                   </svg>
                   <span>شاشة QR</span>
                </a>
                @endcan

                @can('departments.manage')
                <a href="{{ route('employees.index') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('employees.*') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                   </svg>
                   <span>الموظفين</span>
                </a>
                
                <a href="{{ route('departments.index') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('departments.*') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                   </svg>
                   <span>الأقسام</span>
                </a>
                @endcan

                @can('roles.manage')
                <a href="{{ route('roles.index') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('roles.*') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                   </svg>
                   <span>الأدوار</span>
                </a>
                @endcan
            </div>
        </div>

        <!-- Section: Reports -->
        @can('reports.view')
        <div x-data="{ expanded: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
            <div class="px-2 mb-2 mt-4 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex justify-between items-center cursor-pointer" @click="expanded = !expanded">
                <span>التقارير</span>
                <svg :class="{'rotate-180': expanded}" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <div x-show="expanded" x-collapse class="space-y-1">
                <a href="{{ route('reports.daily') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('reports.daily') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                   </svg>
                   <span>تقرير يومي</span>
                </a>
                <a href="{{ route('reports.monthly') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('reports.monthly') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                   </svg>
                   <span>تقرير شهري</span>
                </a>
                <a href="{{ route('reports.employee.search') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('reports.employee.search') || request()->routeIs('employee.report') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                   </svg>
                   <span>تقرير موظف</span>
                </a>
            </div>
        </div>
        @endcan

        <!-- Section: System -->
        <div>
            <div class="px-2 mb-2 mt-4 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                النظام
            </div>
            <div class="space-y-1">
                @can('users.manage')
                <a href="{{ route('users.index') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('users.*') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                   </svg>
                   <span>المستخدمين</span>
                </a>
                
                <a href="{{ route('statuses.index') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('statuses.*') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                   </svg>
                   <span>حالات الحضور</span>
                </a>

                <a href="{{ route('settings.index') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('settings.*') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                   </svg>
                   <span>الإعدادات</span>
                </a>
                @endcan
                
                @can('audit.view')
                <a href="{{ route('audit.index') }}" 
                   class="flex items-center px-4 py-2.5 text-base font-bold rounded-lg transition-colors duration-200
                   {{ request()->routeIs('audit.*') 
                      ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200' 
                      : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                   <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                   </svg>
                   <span>سجل التدقيق</span>
                </a>
                @endcan
            </div>
        </div>

    </nav>

    <!-- Footer: User & Mode -->
    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
        
        <!-- Dark Mode Toggle -->
        <button onclick="toggleDarkMode()" 
                class="w-full flex items-center justify-center mb-4 px-3 py-2 rounded-lg text-sm font-medium border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
            <span id="darkModeIcon" class="ml-2">🌙</span>
            <span>الوضع الليلي</span>
        </button>

        <div class="flex items-center gap-3">
             <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-lg">
                {{ substr(Auth::user()->name, 0, 1) }}
             </div>
             <div class="flex-1 min-w-0">
                 <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                     {{ Auth::user()->name }}
                 </p>
                 <a href="{{ route('profile.edit') }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                     المعلومات الشخصية
                 </a>
             </div>
             <form method="POST" action="{{ route('logout') }}">
                 @csrf
                 <button type="submit" class="p-2 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors duration-200">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                     </svg>
                 </button>
             </form>
        </div>
    </div>
</div>

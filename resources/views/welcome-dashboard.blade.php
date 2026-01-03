<x-app-layout>
    {{-- استدعاء خط Cairo --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body, h1, h2, h3, p, a, span, div, button {
            font-family: 'Cairo', sans-serif !important;
        }
        /* زخرفة الخط المنقط في الخلفية */
        .dashed-circle {
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            position: absolute;
            animation: spin 60s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        
        /* حركة طفو للصورة */
        .floating-img {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
    </style>

    <div class="relative min-h-screen bg-blue-600 overflow-hidden dir-rtl">
        
        {{-- الخلفية: تدرج لوني أزرق + أشكال زخرفية --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 z-0"></div>
        
        {{-- الدوائر الزخرفية (محاكاة للصورة الأصلية) --}}
        <div class="dashed-circle w-[500px] h-[500px] -top-20 -right-20 z-0 opacity-30"></div>
        <div class="dashed-circle w-[300px] h-[300px] top-40 right-20 z-0 opacity-20"></div>

        {{-- شريط التنقل العلوي (بسيط) --}}
        <nav class="relative z-20 w-full px-6 py-6 flex justify-between items-center max-w-7xl mx-auto">
            <div class="text-2xl font-black text-white tracking-wider flex items-center gap-2">
                <span class="text-yellow-400 text-4xl">.</span>نظام الحضور
            </div>
            
            <div class="hidden md:flex gap-6 text-blue-100 font-medium text-sm">
                @can('reports.view') <a href="{{ route('reports.daily') }}" class="hover:text-white transition">التقارير</a> @endcan
                @can('departments.manage') <a href="{{ route('employees.index') }}" class="hover:text-white transition">الموظفين</a> @endcan
                <a href="{{ route('dashboard') }}" class="hover:text-white transition">لوحة التحكم</a>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-white text-sm font-bold bg-blue-800/50 px-4 py-2 rounded-full border border-blue-500/30">
                    {{ now()->format('Y-m-d') }} 📅
                </div>
            </div>
        </nav>

        {{-- المحتوى الرئيسي: Grid لتقسيم الشاشة --}}
        <div class="relative z-10 max-w-7xl mx-auto px-6 pt-10 pb-20 flex flex-col-reverse lg:grid lg:grid-cols-2 gap-12 items-center h-full">
            
            {{-- العمود الأيمن: النصوص (يمثل الجزء الأيسر في الصورة الأصلية لأننا عربي) --}}
            <div class="text-right w-full space-y-8 mt-10 lg:mt-0">
                
                {{-- الشارة --}}
                <span class="inline-block bg-blue-800 text-blue-200 text-xs font-bold px-3 py-1 rounded-full mb-2 border border-blue-500">
                     أهلاً بعودتك، {{ Auth::user()->name }} 👋
                </span>

                {{-- العنوان --}}
                <h1 class="text-5xl lg:text-7xl font-black text-white leading-[1.2]">
                    تحكم سهل <br> 
                    <span class="text-blue-200">في حضور فddddddddddddddddddddريقك</span>
                </h1>

                {{-- الوصف --}}
                <p class="text-lg text-blue-100 leading-relaxed max-w-lg font-medium opacity-90">
                    نظام متكامل لتسجيل الحضور والانصراف، إدارة الموظفين، واستخراج التقارير بضغطة زر واحدة. صمم لتبسيط أعمالك اليومية.
                </p>

                {{-- أزرار الإجراء --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="{{ route('attendance.index') }}" 
                       class="bg-yellow-500 hover:bg-yellow-400 text-blue-900 text-lg font-bold py-4 px-10 rounded-lg shadow-lg shadow-yellow-500/30 transform hover:-translate-y-1 transition duration-300 text-center flex items-center justify-center gap-2">
                        سجل حضورك الآن
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                    
                    <a href="{{ route('dashboard') }}" 
                       class="bg-transparent border-2 border-white/20 hover:bg-white/10 text-white text-lg font-semibold py-4 px-8 rounded-lg transition duration-300 text-center">
                        لوحة القيادة
                    </a>
                </div>

                {{-- ميزات سريعة (بديل للفوتر القديم) --}}
                <div class="pt-8 grid grid-cols-3 gap-4 border-t border-white/10 mt-8">
                    @can('reports.view')
                    <div class="text-white">
                        <h4 class="font-bold text-xl text-yellow-400">📊</h4>
                        <p class="text-sm text-blue-200 mt-1">تقارير ذكية</p>
                    </div>
                    @endcan
                    
                    @can('departments.manage')
                    <div class="text-white">
                        <h4 class="font-bold text-xl text-yellow-400">👥</h4>
                        <p class="text-sm text-blue-200 mt-1">إدارة الموظفين</p>
                    </div>
                    @endcan

                    <div class="text-white">
                        <h4 class="font-bold text-xl text-yellow-400">⚡</h4>
                        <p class="text-sm text-blue-200 mt-1">سرعة وأداء</p>
                    </div>
                </div>
            </div>

            {{-- العمود الأيسر: الصورة (بديل صورة اليد والموبايل) --}}
            <div class="w-full relative flex justify-center lg:justify-end">
                {{-- خلفية دائرية خفيفة خلف الصورة --}}
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-blue-500/20 rounded-full blur-3xl -z-10"></div>
                
                {{-- الصورة التوضيحية (استخدمت صورة تعبيرية للموبايل/الداشبورد) --}}
                <div class="relative floating-img transform rotate-y-12 perspective-1000">
                    {{-- يمكنك استبدال الرابط بصورة خاصة بنظامك --}}
              
                    
                    {{-- عناصر عائمة (مثل الرسائل في الصورة الأصلية) --}}
                    <div class="absolute -top-10 -right-10 bg-white p-4 rounded-2xl shadow-xl animate-bounce" style="animation-duration: 3s;">
                        <span class="text-2xl">✅</span>
                    </div>
                    <div class="absolute bottom-20 -left-10 bg-white p-3 rounded-xl shadow-xl animate-bounce" style="animation-duration: 4s;">
                        <span class="text-2xl">⏰</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
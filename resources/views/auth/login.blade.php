<x-guest-layout>
    {{-- استدعاء خط Cairo --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <style>
        body, input, label, button, span, a, div {
            font-family: 'Cairo', sans-serif !important;
        }
        /* خلفية الدوائر المنقطة (نفس المستخدمة في الصفحة الرئيسية) */
        .dashed-circle {
            border: 2px dashed rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            position: absolute;
            animation: spin 60s linear infinite;
            z-index: 0;
            pointer-events: none;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>

    <div class="min-h-screen w-full flex items-center justify-center relative bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 overflow-hidden dir-rtl">
        
        {{-- الزخارف الخلفية --}}
        <div class="dashed-circle w-[600px] h-[600px] -top-20 -right-20 opacity-20"></div>
        <div class="dashed-circle w-[400px] h-[400px] bottom-0 left-0 opacity-20"></div>

        {{-- بطاقة تسجيل الدخول --}}
        <div class="relative z-10 w-full max-w-md px-6 py-4">
            
            {{-- الشعار أو العنوان العلوي --}}
            <div class="text-center mb-8">
                <h2 class="text-3xl font-black text-white mb-2">تسجيل الدخول</h2>
                <p class="text-blue-200 text-sm">مرحباً بعودتك! الرجاء إدخال بياناتك</p>
            </div>

            {{-- البطاقة البيضاء --}}
            <div class="bg-white rounded-2xl shadow-2xl p-8 transform transition-all hover:scale-[1.01]">
                
                <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    <!-- Email Address -->
    <div>
        <x-input-label for="email" :value="__('البريد الإلكتروني')" class="text-gray-700 font-bold mb-1" />
        <div class="relative">
            {{-- 
               التعديل هنا:
               1. استبدال px-4 بـ pr-12 pl-4
               (pr-12) تعطي مسافة كبيرة من اليمين للأيقونة
            --}}
            <x-text-input id="email" 
                          class="block mt-1 w-full bg-gray-50 border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 rounded-lg py-3 pr-12 pl-4 transition-colors" 
                          type="email" 
                          name="email" 
                          :value="old('email')" 
                          required autofocus autocomplete="username" 
                          placeholder="name@company.com" />
            
            {{-- 
               التعديل هنا:
               1. تغيير left-0 إلى right-0
               2. تغيير pl-3 إلى pr-3
               لنقل الأيقونة لليمين
            --}}
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
            </div>
        </div>
        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
    </div>

    <!-- Password -->
    <div>
        <x-input-label for="password" :value="__('كلمة المرور')" class="text-gray-700 font-bold mb-1" />
        <div class="relative">
            {{-- نفس التعديل: مسافة من اليمين pr-12 --}}
            <x-text-input id="password" 
                          class="block mt-1 w-full bg-gray-50 border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 rounded-lg py-3 pr-12 pl-4 transition-colors"
                          type="password"
                          name="password"
                          required autocomplete="current-password" 
                          placeholder="********" />
             
             {{-- نقل الأيقونة لليمين --}}
             <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
    </div>

    {{-- بقية الفورم (تذكرني والزر) --}}
    <div class="flex items-center justify-between mt-4">
        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-yellow-500 shadow-sm focus:ring-yellow-500 cursor-pointer" name="remember">
            <span class="ms-2 text-sm text-gray-600 group-hover:text-gray-900 transition">{{ __('تذكرني') }}</span>
        </label>

        @if (Route::has('password.request'))
            <a class="text-sm text-blue-600 hover:text-blue-800 font-semibold transition" href="{{ route('password.request') }}">
                {{ __('نسيت كلمة المرور؟') }}
            </a>
        @endif
    </div>

    <div class="pt-2">
        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-blue-900 bg-yellow-500 hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-300 shadow-[0_4px_14px_0_rgba(234,179,8,0.39)]">
            {{ __('تسجيل سسسسسسسسسسسسالدخول') }}
        </button>
    </div>
</form>
            </div>
            
            {{-- رابط العودة --}}
            <div class="text-center mt-6">
                <a href="/" class="text-blue-200 hover:text-white text-sm font-medium transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    العودة للصفحة الرئيسية
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>
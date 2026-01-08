<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الحضور</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <style>
        :root {
            --primary: #6750A4;
            --on-primary: #FFFFFF;
            --primary-container: #EADDFF;
            --on-primary-container: #21005D;
            --surface: #FEF7FF;
            --on-surface: #1D1B20;
            --surface-variant: #E7E0EC;
            --outline: #79747E;
            --error: #B3261E;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .md-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            padding: 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Material Input */
        .md-input-container {
            position: relative;
            margin-bottom: 24px;
            text-align: right;
        }
        
        .md-input {
            width: 100%;
            padding: 16px;
            border: 1px solid var(--outline);
            border-radius: 4px;
            background: transparent;
            font-family: inherit;
            font-size: 16px;
            color: var(--on-surface);
            transition: all 0.2s;
            box-sizing: border-box;
        }
        
        .md-input:focus {
            outline: none;
            border-color: var(--primary);
            border-width: 2px;
            padding: 15px; /* Adjust for border width */
        }

        .md-input:focus + label,
        .md-input:not(:placeholder-shown) + label {
            top: -8px;
            background: white;
            padding: 0 4px;
            font-size: 12px;
            color: var(--primary);
        }

        .md-input-label {
            position: absolute;
            top: 16px;
            right: 12px;
            color: var(--outline);
            pointer-events: none;
            transition: all 0.2s;
            background-color: transparent;
        }

        /* Material Button */
        .md-btn {
            background-color: var(--primary);
            color: var(--on-primary);
            border: none;
            border-radius: 100px;
            padding: 12px 24px;
            font-family: inherit;
            font-weight: 500;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
            transition: box-shadow 0.2s, background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .md-btn:hover {
            box-shadow: 0 4px 8px 3px rgba(0,0,0,0.15);
            background-image: linear-gradient(rgba(255,255,255,0.08), rgba(255,255,255,0.08));
        }

        .md-btn:active {
            box-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        .md-btn:disabled {
            background-color: rgba(29, 27, 32, 0.12);
            color: rgba(29, 27, 32, 0.38);
            box-shadow: none;
            cursor: not-allowed;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 24px;
        }
        .badge.check_in {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .badge.check_out {
            background-color: #FFEDD5;
            color: #9A3412;
        }

        /* Timer Linear Progress */
        .progress-container {
            height: 4px;
            background-color: #E7E0EC;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 24px;
        }
        .progress-bar {
            height: 100%;
            background-color: var(--primary);
            transition: width 1s linear;
        }

        /* Alerts */
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: right;
        }
        .alert-error {
            background-color: #F9DEDC;
            color: #410E0B;
        }
        .alert-warning {
            background-color: #FFEDD5;
            color: #7C2D12;
        }

        /* Header Icon */
        .header-icon {
            width: 64px;
            height: 64px;
            border-radius: 24px;
            background-color: var(--primary-container);
            color: var(--on-primary-container);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .header-icon span {
            font-size: 32px;
        }

        /* Animations */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .shake { animation: shake 0.3s ease-in-out; }
    </style>
</head>
<body>

    @if(isset($error) || isset($expired))
        <!-- Expired State -->
        <div class="md-card">
            <div class="header-icon" style="background-color: #F9DEDC; color: #B3261E;">
                <span class="material-icons-round">gpp_bad</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">رمز غير صالح</h1>
            <p class="text-gray-500 mb-6">{{ $error ?? 'انتهت صلاحية رمز QR' }}</p>
            
            <p class="text-sm bg-gray-50 p-4 rounded-lg text-gray-600">
                يرجى العودة للشاشة الرئيسية ومسح رمز QR جديد.
            </p>
        </div>
    @else
        <!-- Active State -->
        <div class="md-card">
            
            <!-- Icon -->
            <div class="header-icon" style="
                background-color: {{ $type === 'check_in' ? '#D1FAE5' : '#FFEDD5' }};
                color: {{ $type === 'check_in' ? '#065F46' : '#9A3412' }};">
                <span class="material-icons-round">{{ $type === 'check_in' ? 'login' : 'logout' }}</span>
            </div>

            <!-- Title -->
            <h1 class="text-xl font-bold text-gray-900 mb-1">
                {{ $type === 'check_in' ? 'تسجيل الحضور' : 'تسجيل الانصراف' }}
            </h1>
            <p class="text-gray-500 text-sm mb-4">{{ $department ? $department->name : 'عام (كل الأقسام)' }}</p>

            <!-- Alerts -->
            @if(session('error'))
                <div class="alert alert-error">
                    <span class="material-icons-round">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <span class="material-icons-round">warning</span>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            <form action="{{ route('attend.submit', $qrToken->token) }}" method="POST">
                @csrf
                
                <div class="md-input-container">
                    <input 
                        type="text" 
                        name="employee_number" 
                        id="employee_number"
                        class="md-input" 
                        placeholder=" "
                        value="{{ old('employee_number') }}"
                        required 
                        autofocus
                        inputmode="numeric"
                        autocomplete="off"
                    >
                    <label for="employee_number" class="md-input-label">الرقم الوظيفي</label>
                </div>

                <button type="submit" class="md-btn" style="background-color: {{ $type === 'check_in' ? '#059669' : '#EA580C' }};">
                    <span class="material-icons-round">check_circle</span>
                    <span>{{ $type === 'check_in' ? 'تأكيد الحضور' : 'تأكيد الانصراف' }}</span>
                </button>
            </form>

            <!-- Timer -->
            <div class="mt-6">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>صلاحية الرمز</span>
                    <span id="timerText">{{ $expiresIn }} ثانية</span>
                </div>
                <div class="progress-container">
                    <div id="progressBar" class="progress-bar" style="width: 100%; background-color: {{ $type === 'check_in' ? '#059669' : '#EA580C' }};"></div>
                </div>
            </div>

        </div>

        <script>
            let totalSeconds = {{ $expiresIn }}; // Initial duration
            let remainingSeconds = {{ $expiresIn }};
            const timerText = document.getElementById('timerText');
            const progressBar = document.getElementById('progressBar');
            const inputs = document.querySelectorAll('input, button');
            
            // Initial Width
            // If totalSeconds was passed originally (e.g. 60), use that. 
            // But here we only get remainingSeconds. Assuming max was same as remaining for simplified UI bar or just depletes to 0.
            // Better UX: Width is 100% now, goes to 0 over remaining seconds.
            
            const timer = setInterval(() => {
                remainingSeconds--;
                
                // Update Text
                timerText.textContent = remainingSeconds + " ثانية";
                
                // Update Bar width
                const percentage = (remainingSeconds / totalSeconds) * 100;
                progressBar.style.width = percentage + "%";

                if (remainingSeconds <= 0) {
                    clearInterval(timer);
                    disableForm();
                }
            }, 1000);

            function disableForm() {
                timerText.textContent = "انتهى الوقت";
                timerText.style.color = "var(--error)";
                progressBar.style.backgroundColor = "var(--error)";
                progressBar.style.width = "100%";
                
                inputs.forEach(input => input.disabled = true);
                
                const card = document.querySelector('.md-card');
                const overlay = document.createElement('div');
                overlay.className = 'absolute inset-0 bg-white/80 flex items-center justify-center backdrop-blur-sm z-50';
                overlay.innerHTML = `
                    <div class="text-center animate-pulse">
                        <span class="material-icons-round text-4xl text-red-600 mb-2">timer_off</span>
                        <p class="font-bold text-gray-800">انتهت الصلاحية</p>
                        <a href="" class="text-blue-600 text-sm mt-2 block hover:underline" onclick="location.reload()">تحديث الصفحة</a>
                    </div>
                `;
                card.appendChild(overlay);
            }
        </script>
    @endif

</body>
</html>

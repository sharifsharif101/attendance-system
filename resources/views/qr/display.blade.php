<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>شاشة تسجيل الحضور - QR Code</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --md-sys-color-primary: #6750A4;
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-primary-container: #EADDFF;
            --md-sys-color-on-primary-container: #21005D;
            --md-sys-color-error: #B3261E;
            --md-sys-color-surface: #FEF7FF;
            --md-sys-color-surface-variant: #E7E0EC;
            --md-sys-color-on-surface-variant: #49454F;
            --md-sys-color-outline: #79747E;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: auto;
            padding: 16px;
            box-sizing: border-box;
        }

        /* Material Card */
        .md-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 4px 8px 3px rgba(0,0,0,0.15), 0 1px 3px rgba(0,0,0,0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        /* Material Ripple */
        .ripple {
            position: relative;
            overflow: hidden;
            transform: translate3d(0, 0, 0);
        }
        .ripple:after {
            content: "";
            display: block;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, #000 10%, transparent 10.01%);
            background-repeat: no-repeat;
            background-position: 50%;
            transform: scale(10, 10);
            opacity: 0;
            transition: transform .5s, opacity 1s;
        }
        .ripple:active:after {
            transform: scale(0, 0);
            opacity: .2;
            transition: 0s;
        }

        /* Pill Button Toggle */
        .pill-toggle {
            display: inline-flex;
            position: relative;
            padding: 4px;
            background: #E7E0EC;
            border-radius: 100px;
            width: 100%;
        }

        .pill-toggle-highlight {
            position: absolute;
            top: 4px;
            bottom: 4px;
            width: calc(50% - 4px);
            background: #6750A4;
            border-radius: 100px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 0;
            box-shadow: 0 2px 8px rgba(103, 80, 164, 0.3);
        }

        /* RTL: Default position is right (check_in) */
        [dir="rtl"] .pill-toggle-highlight {
            right: 4px;
            left: auto;
        }

        /* When check_out is active, move to left in RTL */
        [dir="rtl"] .pill-toggle-highlight.checkout-active {
            right: 50%;
        }

        .pill-toggle-btn {
            flex: 1;
            border: none;
            background: transparent;
            padding: 12px 20px;
            border-radius: 100px;
            font-family: 'Tajawal', sans-serif;
            font-weight: 600;
            font-size: 15px;
            color: #49454F;
            cursor: pointer;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            z-index: 1;
            position: relative;
        }

        .pill-toggle-btn.active {
            color: #FFFFFF;
        }

        .pill-toggle-btn:hover:not(.active) {
            background: rgba(103, 80, 164, 0.08);
        }

        /* Timer Ring */
        .timer-ring circle {
            transition: stroke-dashoffset 0.1s linear;
        }

        /* Floating Select */
        .md-outlined-select {
            position: relative;
        }
        .md-outlined-select select {
            width: 100%;
            padding: 16px 14px;
            border: 1px solid var(--md-sys-color-outline);
            border-radius: 4px;
            background: transparent;
            font-family: 'Tajawal', sans-serif;
            font-size: 16px;
            appearance: none;
            cursor: pointer;
        }
        .md-outlined-select select:focus {
            outline: none;
            border-color: var(--md-sys-color-primary);
            border-width: 2px;
            padding: 15px 13px; /* Adjust for border width */
        }
        .md-outlined-select label {
            position: absolute;
            top: -8px;
            right: 12px;
            background: white;
            padding: 0 4px;
            font-size: 12px;
            color: var(--md-sys-color-primary);
        }
        .md-outlined-select .arrow {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: var(--md-sys-color-on-surface-variant);
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Main Container -->
    <div class="md-card w-full max-w-6xl min-h-[600px] md:h-auto md:max-h-[90vh] flex flex-col md:flex-row shadow-2xl overflow-auto">
        
        <!-- Left Side: Interactive (Settings & Type) -->
        <div class="w-full md:w-2/5 bg-white p-8 md:p-12 border-l border-gray-100 flex flex-col justify-between relative z-10">
            
            <!-- Header -->
            <div class="text-center md:text-right">
                <div class="flex items-center gap-4 mb-2 justify-center md:justify-start">
                    @if($companyLogo)
                        <img src="{{ asset('storage/' . $companyLogo) }}" alt="Logo" class="h-14 w-14 object-contain">
                    @else
                        <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-700">
                            <span class="material-icons-round text-3xl">apartment</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="font-bold text-2xl text-gray-800 leading-tight">{{ $companyName }}</h1>
                        <p class="text-sm text-gray-500 font-medium">نظام تسجيل الحضور الذكي</p>
                    </div>
                </div>
            </div>


            <!-- Controls -->
            <div class="space-y-8 my-8">
                
                <!-- Type Toggle -->
                <div class="flex flex-col gap-3">
                    <label class="text-sm font-medium text-gray-700 px-1">نوع التسجيل</label>
                    <div class="pill-toggle" id="pillToggle">
                        <span class="pill-toggle-highlight {{ $type === 'check_out' ? 'checkout-active' : '' }}" id="pillHighlight"></span>
                        <button class="pill-toggle-btn {{ $type === 'check_in' ? 'active' : '' }}" data-value="check_in" id="checkInBtn">
                            <span class="material-icons-round">login</span>
                            <span>حضور</span>
                        </button>
                        <button class="pill-toggle-btn {{ $type === 'check_out' ? 'active' : '' }}" data-value="check_out" id="checkOutBtn">
                            <span class="material-icons-round">logout</span>
                            <span>انصراف</span>
                        </button>
                    </div>
                </div>

                <!-- Live Clock -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-100 shadow-inner">
                    <div class="text-gray-400 text-sm mb-1 font-medium">الوقت الحالي</div>
                    <div class="text-4xl font-black text-gray-800 tabular-nums tracking-tight dir-ltr" id="currentTime">--:--:--</div>
                    <div class="text-gray-400 text-sm mt-2 font-medium" id="currentDate">--/--/----</div>
                </div>

            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-gray-300 font-medium">
                &copy; {{ date('Y') }} جميع الحقوق محفوظة
            </div>
        </div>

        <!-- Right Side: QR Display -->
        <div class="w-full md:w-3/5 bg-gradient-to-br from-purple-50 to-indigo-50 relative flex items-center justify-center p-6 overflow-hidden">
            
            <!-- Abstract Background Shapes -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-purple-200/50 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-200/50 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>

            <div class="relative z-10 flex flex-col items-center w-full max-w-sm">
                
                <!-- Status Text (Moved to Top) -->
                <div class="mb-4 text-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2 tracking-tight">
                        امسح الرمز لتسجيل <span id="statusText" class="text-purple-600 transition-colors duration-300">--</span>
                    </h2>
                    <p class="text-gray-500 text-base font-medium">وجه كاميرا هاتفك نحو الرمز أعلاه لتسجيل العملية فوراً</p>
                </div>

                <!-- QR Card -->
                <div class="bg-white p-8 rounded-[2rem] shadow-2xl transform transition-all hover:scale-[1.02] duration-500 w-full aspect-square flex items-center justify-center relative group">
                    <!-- Glow effect behind -->
                    <div class="absolute inset-0 bg-white rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] transition-shadow duration-500 group-hover:shadow-[0_30px_70px_-15px_rgba(0,0,0,0.2)]"></div>
                    
                    <div id="qrCode" class="w-full h-full flex items-center justify-center bg-white relative z-10">
                        <div class="animate-pulse bg-gray-100 w-full h-full rounded-2xl"></div>
                    </div>
                </div>

                <!-- Timer -->
                <div class="mt-6 relative hover:scale-110 transition-transform cursor-default">
                    <svg class="timer-ring w-16 h-16 transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(0,0,0,0.05)" stroke-width="8"/>
                        <circle id="timerCircle" cx="50" cy="50" r="45" fill="none" stroke="#6750A4" stroke-width="8" 
                                stroke-dasharray="283" stroke-dashoffset="0" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span id="timerText" class="text-xl font-bold text-purple-800">{{ $refreshSeconds }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script>
        const refreshSeconds = {{ $refreshSeconds }};
        let currentType = '{{ $type }}';
        let countdown = refreshSeconds;
        let countdownInterval = null;

        // UI Elements
        const qrCodeEl = document.getElementById('qrCode');
        const timerTextEl = document.getElementById('timerText');
        const timerCircleEl = document.getElementById('timerCircle');
        const statusTextEl = document.getElementById('statusText');
        
        // Update Status Text
        function updateStatusUI() {
            statusTextEl.textContent = currentType === 'check_in' ? 'الحضور' : 'الانصراف';
            
            // Adjust colors based on type
            const root = document.documentElement;
            if(currentType === 'check_in') {
                root.style.setProperty('--md-sys-color-primary', '#059669'); // Green
                root.style.setProperty('--md-sys-color-primary-container', '#D1FAE5');
            } else {
                root.style.setProperty('--md-sys-color-primary', '#EA580C'); // Orange
                root.style.setProperty('--md-sys-color-primary-container', '#FFEDD5');
            }
        }
        
        // Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('currentTime').textContent = 
                now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('currentDate').textContent = 
                now.toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        setInterval(updateClock, 1000);
        updateClock(); // Init

        // Timer Logic
        function updateTimer() {
            timerTextEl.textContent = countdown;
            const offset = 283 - (283 * countdown / refreshSeconds);
            timerCircleEl.style.strokeDashoffset = offset;

            if (countdown <= 0) {
                generateQR();
                countdown = refreshSeconds;
            } else {
                countdown--;
            }
        }

        // Generate QR
        async function generateQR() {
            // Loading state
            qrCodeEl.style.opacity = '0.5';
            
            try {
                const response = await fetch('{{ route("qr.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        department_id: null,
                        type: currentType
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    qrCodeEl.innerHTML = data.qr_code;
                    qrCodeEl.style.opacity = '1';
                    // Reset timer purely visually if we wanted, but the logic handles loop
                    countdown = data.refresh_seconds; 
                } else {
                    qrCodeEl.innerHTML = '<span class="text-red-500 material-icons-round text-4xl">error</span>';
                }
            } catch (error) {
                console.error('Error:', error);
                qrCodeEl.innerHTML = '<span class="text-red-500 material-icons-round text-4xl">wifi_off</span>';
            }
        }

        // Event Listeners for Pill Toggle
        const pillBtns = document.querySelectorAll('.pill-toggle-btn');
        const pillHighlight = document.getElementById('pillHighlight');
        
        pillBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active from all
                pillBtns.forEach(b => b.classList.remove('active'));
                // Add active to clicked
                this.classList.add('active');
                // Update type
                currentType = this.dataset.value;
                
                // Move highlight
                if (currentType === 'check_out') {
                    pillHighlight.classList.add('checkout-active');
                } else {
                    pillHighlight.classList.remove('checkout-active');
                }
                
                updateStatusUI();
                generateQR();
            });
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateStatusUI();
            generateQR();
            countdownInterval = setInterval(updateTimer, 1000);
        });
    </script>
</body>
</html>

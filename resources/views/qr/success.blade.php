<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>تم التسجيل بنجاح</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <style>
        :root {
            --primary: #10B981; /* Green */
            --on-primary: #FFFFFF;
            --primary-container: #D1FAE5;
            --surface: #FFFFFF;
            --surface-variant: #F3F4F6;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            margin: 0;
            overflow: hidden;
        }

        /* Confetti Animation Background */
        .confetti {
            position: absolute;
            top: -10px;
            width: 10px;
            height: 10px;
            background-color: var(--primary);
            animation: fall linear forwards;
            opacity: 0;
        }

        @keyframes fall {
            0% { transform: translateY(-10vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }

        .md-card {
            background: var(--surface);
            border-radius: 28px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 380px;
            padding: 40px 32px;
            text-align: center;
            position: relative;
            z-index: 10;
            transform: scale(0.9);
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes popIn {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .icon-wrapper {
            width: 96px;
            height: 96px;
            background: var(--primary-container);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            position: relative;
        }
        
        .material-icons-round {
            font-size: 48px;
            animation: checkmark 0.6s ease-in-out forwards;
        }

        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .ripple-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid var(--primary);
            animation: ripple 1.5s infinite;
            opacity: 0;
        }

        @keyframes ripple {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        p.subtitle {
            color: var(--text-secondary);
            font-size: 16px;
            margin-bottom: 32px;
        }

        .info-container {
            background-color: var(--surface-variant);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .employee-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .time {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary);
            margin: 12px 0;
            font-variant-numeric: tabular-nums;
            letter-spacing: -1px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-badge.success {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-badge.warning {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-badge.info {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .btn-close {
            background: transparent;
            color: var(--text-secondary);
            border: none;
            padding: 12px;
            font-family: inherit;
            cursor: pointer;
            width: 100%;
            border-radius: 8px;
            transition: background 0.2s;
        }
        
        .btn-close:hover {
            background: rgba(0,0,0,0.05);
            color: var(--text-primary);
        }

    </style>
</head>
<body>

    <!-- Simple JS confetti generator -->
    <script>
        for(let i=0; i<30; i++) {
            const confetti = document.createElement('div');
            confetti.classList.add('confetti');
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.animationDuration = (Math.random() * 2 + 1) + 's';
            confetti.style.backgroundColor = ['#10B981', '#3B82F6', '#F59E0B'][Math.floor(Math.random()*3)];
            document.body.appendChild(confetti);
        }
    </script>

    <div class="md-card">
        <div class="icon-wrapper">
            <div class="ripple-ring"></div>
            <span class="material-icons-round">check_circle</span>
        </div>

        <h1>تم التسجيل بنجاح!</h1>
        <p class="subtitle">
            {{ $type === 'check_in' ? 'أهلاً بك، نتمنى لك يوماً سعيداً' : 'شكراً لك، في حفظ الله' }}
        </p>

        <div class="info-container">
            <div class="employee-name">{{ $name }}</div>
            <div class="time">{{ $time }}</div>
            
            @if($type === 'check_in')
                @if(($status ?? 'present') === 'late')
                    <div class="status-badge warning">
                        <span class="material-icons-round" style="font-size: 16px;">warning</span>
                        <span>تسجيل متأخر</span>
                    </div>
                @else
                    <div class="status-badge success">
                        <span class="material-icons-round" style="font-size: 16px;">verified</span>
                        <span>حضور في الوقت</span>
                    </div>
                @endif
            @else
                <div class="status-badge info">
                    <span class="material-icons-round" style="font-size: 16px;">logout</span>
                    <span>تم الانصراف</span>
                </div>
            @endif
        </div>

        <button onclick="window.close()" class="btn-close">إغلاق الصفحة</button>
    </div>

</body>
</html>

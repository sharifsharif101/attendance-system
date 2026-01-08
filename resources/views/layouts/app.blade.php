<!DOCTYPE html>
<html lang="ar" dir="rtl" class="">

<head>
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>نظام الحضور والغياب</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">

    <div class="flex h-screen bg-gray-100 dark:bg-gray-900 overflow-hidden">
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow z-10">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900">
                <div class="py-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    <script>
        function toggleDarkMode() {
            const html = document.documentElement;
            const icon = document.getElementById('darkModeIcon');

            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
                icon.textContent = '🌙';
            } else {
                html.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
                icon.textContent = '☀️';
            }
        }

        // تحديث الأيقونة عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            const icon = document.getElementById('darkModeIcon');
            if (icon && localStorage.getItem('darkMode') === 'true') {
                icon.textContent = '☀️';
            }
        });
    </script>
 <script>
    // إخفاء رسائل النجاح تلقائياً بعد 3 ثواني
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('[data-alert="success"]');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 3000);
    });
</script>

<script>
    function sortTable(th) {
        const table = th.closest('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const index = th.cellIndex; // استخدام فهرس الخلية الحقيقي لضمان الدقة
        const type = th.getAttribute('data-type') || 'text';
        
        // تحديد الاتجاه
        const order = th.getAttribute('data-order') === 'asc' ? 'desc' : 'asc';
        
        // تحديث الأيقونات
        table.querySelectorAll('th').forEach(header => {
            header.setAttribute('data-order', '');
            const span = header.querySelector('.sort-icon');
            if(span) span.innerText = '↕';
        });
        
        th.setAttribute('data-order', order);
        const currentSpan = th.querySelector('.sort-icon');
        if(currentSpan) currentSpan.innerText = order === 'asc' ? '↑' : '↓';

        rows.sort((rowA, rowB) => {
            // التحقق من وجود الخلية قبل قراءتها لتجنب الأخطاء
            const cellA = rowA.cells[index] ? rowA.cells[index].innerText.trim() : '';
            const cellB = rowB.cells[index] ? rowB.cells[index].innerText.trim() : '';

            if (type === 'number') {
                // تنظيف الرقم من النصوص وتحويل الأرقام العربية إلى إنجليزية إن وجدت
                const cleanNumber = (str) => {
                    return parseFloat(
                        str.replace(/[٠-٩]/g, d => "٠١٢٣٤٥٦٧٨٩".indexOf(d)) // تحويل عربي لإنجليزي
                           .replace(/[^0-9.-]+/g, "") // حذف أي حروف
                    ) || 0;
                };
                
                const a = cleanNumber(cellA);
                const b = cleanNumber(cellB);
                return order === 'asc' ? a - b : b - a;
            } else {
                // ترتيب النصوص
                return order === 'asc' 
                    ? cellA.localeCompare(cellB, 'ar') 
                    : cellB.localeCompare(cellA, 'ar');
            }
        });

        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));
    }
</script>

</body>

</html>

# دليل تجهيز النسخة المحمولة (Packaging Guide)

هذا الدليل موجه **لك أنت (المبرمج)** لتجهيز ملف "Portable" للعميل. العميل لن يقوم بذلك.

## الفكرة
سنقوم بتجهيز نسخة XAMPP تعمل دون تثبيت، ونضع المشروع بداخلها، ونضبط الإعدادات مسبقاً، ثم نضغطها ونرسلها للعميل.

---

## الخطوات (قم بها على جهازك)

1.  **تحميل XAMPP Portable**:
    *   حمل نسخة **XAMPP Portable** (بصيغة Zip أو 7z) من [apachefriends.org](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/) - ابحث عن "Portable" في الاسم.
    *   فك الضغط في مجلد مؤقت مثلاً `C:\Temp\xampp`.

2.  **تنظيف XAMPP**:
    *   ادخل للمجلد `xampp` واحذف المجلدات غير الضرورية لتقليل الحجم (مثل `security`, `webalizer`، `fake`, `tomcat` إذا وجدت).
    *   المهم بقاء `apache`, `php`, `mysql`.

3.  **وضع المشروع**:
    *   انسخ مجلد المشروع `attendance-system` بالكامل وضعه داخل `C:\Temp\xampp\htdocs\`.
    *   ليكون المسار النهائي: `xampp\htdocs\attendance-system`.

4.  **تثبيت المكاتب (Production)**:
    *   افتح الـ CMD في مسار المشروع داخل xampp (`xampp\htdocs\attendance-system`).
    *   نفذ الأمر لتجهيز النسخة النهائية:
        ```bash
        composer install --no-dev --optimize-autoloader
        npm run build
        ```
    *   *(ملاحظة: تحتاج لـ Composer و Node على جهازك الحالي لهذه الخطوة)*.

5.  **إعداد قاعدة البيانات**:
    *   شغل `xampp-control.exe` وافتح MySQL و Apache.
    *   اذهب لـ phpMyAdmin وأنشئ قاعدة البيانات `attendance_db`.
    *   نفذ الـ migration:
        ```bash
        php artisan migrate --seed --force
        ```
    *   أغلق السيرفرات تماماً (Stop Apache & MySQL) وأغلق لوحة التحكم.

6.  **تعديل إعدادات Apache (ليعمل مباشرة على السيرفر)**:
    *   افتح الملف: `xampp\apache\conf\httpd.conf`.
    *   ابحث عن `DocumentRoot`. غيّر المسار ليكون:
        ```apache
        DocumentRoot "/xampp/htdocs/attendance-system/public"
        <Directory "/xampp/htdocs/attendance-system/public">
        ```
    *   هذا سيجعل الموقع يفتح مباشرة دون كتابة `public`.

7.  **إضافة ملف التشغيل السهل**:
    *   أنشئ ملفاً باسم `Start-System.bat` وضعه في المجلد الرئيسي لـ `xampp` (بجانب `setup_xampp.bat` و `xampp-control.exe`).
    *   ضع فيه الكود التالي:
        ```bat
        @echo off
        echo Starting Attendance System...
        cd /d %~dp0
        call setup_xampp.bat
        start apache\bin\httpd.exe
        start mysql\bin\mysqld.exe
        echo Server Started. Opening Browser...
        timeout /t 5
        start http://localhost
        echo.
        echo DO NOT CLOSE THIS WINDOW.
        echo To stop, just close this window.
        pause
        ```

8.  **الضغط والإرسال**:
    *   الآن لديك مجلد `xampp` جاهز.
    *   اضغطه ببرنامج WinRAR أو Zip.
    *   سمّه `Attendance-System.zip`.
    *   أرسله للعميل.

---

## تعليمات للعميل

فقط قل للعميل:
1. فك الضغط.
2. ادخل المجلد واضغط على `Start-System.bat`.
3. سيفتح الموقع تلقائياً.

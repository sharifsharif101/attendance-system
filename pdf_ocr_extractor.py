"""
📚 مستخرج النصوص من PDF باستخدام Gemini AI
النسخة المحسنة مع إدارة ذاكرة ذكية واستئناف تلقائي

الاستخدام:
1. عيّن متغير البيئة GEMINI_API_KEY
2. شغّل: python pdf_ocr_extractor.py --pdf "path/to/book.pdf"
"""

import google.generativeai as genai
from pdf2image import convert_from_path, pdfinfo_from_path
import time
import os
import re
import logging
import argparse
from datetime import datetime

# ================= الإعدادات الافتراضية =================
CONFIG = {
    "dpi": 300,                      # دقة تحويل الصور
    "wait_between_requests": 15,      # الانتظار بين الطلبات (ثانية)
    "wait_on_quota_error": 60,        # الانتظار عند تجاوز الحد
    "wait_on_other_error": 30,        # الانتظار عند خطأ آخر
    "max_retries": 3,                 # أقصى عدد للمحاولات
    "model_name": "gemini-2.0-flash", # اسم الموديل (ثابت وليس latest)
}

# الموجه (Prompt) لضمان الدقة العالية
SYSTEM_PROMPT = """
أنت خبير في التعرف الضوئي على الحروف (OCR) للغة العربية.
مهمتك: استخرج النص الموجود في هذه الصورة حرفياً وبدقة 100%.

القواعد الصارمة:
1. اكتب النص كما هو تماماً (Verbatim) دون أي تغيير أو تلخيص.
2. لا تصحح الأخطاء الإملائية أو النحوية الموجودة في الأصل.
3. حافظ على ترتيب الفقرات.
4. تجاهل أرقام الصفحات والهوامش الجانبية غير المهمة.
5. لا تضف أي مقدمات أو خاتمات (مثل "إليك النص"). ابدأ بالنص مباشرة.
"""

# ================= إعداد نظام السجلات =================
def setup_logging(log_file: str = "extraction_log.txt"):
    """إعداد نظام التسجيل للكونسول والملف"""
    logging.basicConfig(
        level=logging.INFO,
        format="%(asctime)s | %(levelname)s | %(message)s",
        datefmt="%Y-%m-%d %H:%M:%S",
        handlers=[
            logging.FileHandler(log_file, encoding="utf-8"),
            logging.StreamHandler()
        ]
    )
    return logging.getLogger(__name__)


# ================= دوال مساعدة =================
def get_api_key() -> str:
    """الحصول على مفتاح API من متغيرات البيئة"""
    api_key = os.getenv("GEMINI_API_KEY")
    if not api_key:
        raise ValueError(
            "❌ لم يتم العثور على مفتاح API!\n"
            "يرجى تعيين متغير البيئة GEMINI_API_KEY:\n"
            "  Windows: set GEMINI_API_KEY=your_key_here\n"
            "  Linux/Mac: export GEMINI_API_KEY=your_key_here"
        )
    return api_key


def get_last_processed_page(output_file: str) -> int:
    """يكتشف آخر صفحة تم استخراجها من الملف للاستئناف التلقائي"""
    if not os.path.exists(output_file):
        return 0
    
    try:
        with open(output_file, "r", encoding="utf-8") as f:
            content = f.read()
        
        # البحث عن جميع أرقام الصفحات المستخرجة
        matches = re.findall(r"--- صفحة (\d+) ---", content)
        if matches:
            return int(matches[-1])
    except Exception:
        pass
    
    return 0


def get_pdf_page_count(pdf_path: str) -> int:
    """الحصول على عدد صفحات الـ PDF"""
    try:
        info = pdfinfo_from_path(pdf_path)
        return info.get("Pages", 0)
    except Exception as e:
        raise RuntimeError(f"❌ فشل قراءة معلومات PDF: {e}")


def load_single_page(pdf_path: str, page_number: int, dpi: int = 300):
    """
    تحميل صفحة واحدة فقط من الـ PDF (توفير الذاكرة)
    page_number: رقم الصفحة (يبدأ من 1)
    """
    images = convert_from_path(
        pdf_path,
        dpi=dpi,
        first_page=page_number,
        last_page=page_number
    )
    return images[0] if images else None


def extract_text_from_image(model, image, prompt: str) -> str:
    """استخراج النص من صورة باستخدام Gemini"""
    response = model.generate_content([prompt, image])
    return response.text


def save_page_text(output_file: str, page_num: int, text: str):
    """حفظ نص الصفحة في الملف"""
    with open(output_file, "a", encoding="utf-8") as f:
        f.write(f"\n\n--- صفحة {page_num} ---\n\n{text}")


def save_failed_page(output_file: str, page_num: int):
    """تسجيل الصفحة الفاشلة"""
    with open(output_file, "a", encoding="utf-8") as f:
        f.write(f"\n\n--- ⚠️ فشل استخراج الصفحة {page_num} ---\n\n")


# ================= الدالة الرئيسية =================
def process_book(
    pdf_path: str,
    output_file: str,
    start_page: int = None,
    force_start: int = None
):
    """
    المعالجة الرئيسية للكتاب
    
    Args:
        pdf_path: مسار ملف PDF
        output_file: مسار ملف الإخراج
        start_page: صفحة البداية (None = استئناف تلقائي)
        force_start: إجبار البدء من صفحة محددة
    """
    logger = setup_logging()
    
    # التحقق من وجود الملف
    if not os.path.exists(pdf_path):
        logger.error(f"❌ ملف PDF غير موجود: {pdf_path}")
        return
    
    # إعداد الموديل
    logger.info(f"🚀 بدء التشغيل باستخدام الموديل: {CONFIG['model_name']}")
    api_key = get_api_key()
    genai.configure(api_key=api_key)
    model = genai.GenerativeModel(CONFIG['model_name'])
    
    # الحصول على عدد الصفحات
    total_pages = get_pdf_page_count(pdf_path)
    logger.info(f"📖 عدد صفحات الكتاب: {total_pages}")
    
    # تحديد صفحة البداية
    if force_start:
        start = force_start
        logger.info(f"🔄 البدء الإجباري من الصفحة: {start}")
    elif start_page:
        start = start_page
    else:
        # الاستئناف التلقائي
        last_page = get_last_processed_page(output_file)
        start = last_page + 1
        if last_page > 0:
            logger.info(f"🔄 استئناف تلقائي من الصفحة: {start} (آخر صفحة محفوظة: {last_page})")
    
    if start > total_pages:
        logger.info("✅ جميع الصفحات تم استخراجها مسبقاً!")
        return
    
    # إحصائيات
    stats = {
        "success": 0,
        "failed": 0,
        "start_time": datetime.now()
    }
    
    # كتابة رأس الجلسة
    if start == 1 or not os.path.exists(output_file):
        with open(output_file, "w", encoding="utf-8") as f:
            f.write(f"# استخراج نص الكتاب\n")
            f.write(f"# التاريخ: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
            f.write(f"# الملف: {pdf_path}\n")
            f.write("=" * 50 + "\n")
    
    # المعالجة صفحة بصفحة (توفير الذاكرة)
    logger.info("📄 بدء استخراج النصوص...")
    
    for page_num in range(start, total_pages + 1):
        logger.info(f"📄 معالجة الصفحة {page_num}/{total_pages}...")
        
        success = False
        retry_count = 0
        
        while not success and retry_count < CONFIG['max_retries']:
            try:
                # تحميل صفحة واحدة فقط (توفير الذاكرة) ⬅️ التحسين الأهم
                img = load_single_page(pdf_path, page_num, CONFIG['dpi'])
                
                if img is None:
                    logger.warning(f"⚠️ فشل تحميل الصفحة {page_num}")
                    break
                
                # استخراج النص
                page_text = extract_text_from_image(model, img, SYSTEM_PROMPT)
                
                # الحفظ الفوري
                save_page_text(output_file, page_num, page_text)
                
                stats["success"] += 1
                logger.info(f"   ✅ تم الحفظ (انتظار {CONFIG['wait_between_requests']}ث...)")
                
                # تحرير الذاكرة
                del img
                
                time.sleep(CONFIG['wait_between_requests'])
                success = True
                
            except Exception as e:
                retry_count += 1
                error_msg = str(e)
                logger.warning(f"   ⚠️ خطأ في المحاولة {retry_count}: {error_msg[:100]}")
                
                if "429" in error_msg or "quota" in error_msg.lower():
                    wait_time = CONFIG['wait_on_quota_error']
                    logger.info(f"   ⏳ تجاوز الحد. انتظار {wait_time}ث...")
                else:
                    wait_time = CONFIG['wait_on_other_error']
                    logger.info(f"   ⏳ خطأ غير متوقع. انتظار {wait_time}ث...")
                
                time.sleep(wait_time)
        
        if not success:
            stats["failed"] += 1
            save_failed_page(output_file, page_num)
            logger.error(f"❌ فشل استخراج الصفحة {page_num} بعد {CONFIG['max_retries']} محاولات")
    
    # الملخص النهائي
    duration = datetime.now() - stats["start_time"]
    processed = stats["success"] + stats["failed"]
    success_rate = (stats["success"] / processed * 100) if processed > 0 else 0
    
    summary = f"""
╔══════════════════════════════════════════════════╗
║              📊 ملخص الاستخراج                    ║
╠══════════════════════════════════════════════════╣
║  إجمالي الصفحات: {total_pages:>6}                         ║
║  الصفحات المعالجة: {processed:>5}                         ║
║  ✅ الناجحة: {stats['success']:>10}                         ║
║  ❌ الفاشلة: {stats['failed']:>10}                         ║
║  نسبة النجاح: {success_rate:>8.1f}%                        ║
║  المدة: {str(duration).split('.')[0]:>15}                  ║
╠══════════════════════════════════════════════════╣
║  📁 الملف: {output_file:<36} ║
╚══════════════════════════════════════════════════╝
"""
    logger.info(summary)
    
    # حفظ الملخص في نهاية الملف
    with open(output_file, "a", encoding="utf-8") as f:
        f.write(f"\n\n{'=' * 50}\n")
        f.write(f"# انتهى الاستخراج: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        f.write(f"# الصفحات الناجحة: {stats['success']}\n")
        f.write(f"# الصفحات الفاشلة: {stats['failed']}\n")


# ================= نقطة الدخول =================
def main():
    """نقطة الدخول الرئيسية مع دعم سطر الأوامر"""
    parser = argparse.ArgumentParser(
        description="📚 مستخرج النصوص من PDF باستخدام Gemini AI",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
أمثلة:
  python pdf_ocr_extractor.py --pdf book.pdf
  python pdf_ocr_extractor.py --pdf book.pdf --output extracted.txt
  python pdf_ocr_extractor.py --pdf book.pdf --start 50
        """
    )
    
    parser.add_argument(
        "--pdf", "-p",
        required=True,
        help="مسار ملف PDF"
    )
    
    parser.add_argument(
        "--output", "-o",
        default=None,
        help="مسار ملف الإخراج (افتراضي: اسم_الكتاب_extracted.txt)"
    )
    
    parser.add_argument(
        "--start", "-s",
        type=int,
        default=None,
        help="البدء من صفحة محددة (افتراضي: استئناف تلقائي)"
    )
    
    parser.add_argument(
        "--dpi", "-d",
        type=int,
        default=300,
        help="دقة تحويل الصور (افتراضي: 300)"
    )
    
    args = parser.parse_args()
    
    # تحديد ملف الإخراج
    if args.output is None:
        base_name = os.path.splitext(os.path.basename(args.pdf))[0]
        args.output = f"{base_name}_extracted.txt"
    
    # تحديث الإعدادات
    CONFIG['dpi'] = args.dpi
    
    # بدء المعالجة
    process_book(
        pdf_path=args.pdf,
        output_file=args.output,
        force_start=args.start
    )


if __name__ == "__main__":
    main()

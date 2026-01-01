<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            إضافة موظف جديد
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <x-alert />

                    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- البيانات الأساسية --}}
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">📋 البيانات الأساسية</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اسم الموظف <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الرقم الوظيفي <span class="text-red-500">*</span></label>
                                    <input type="text" name="employee_number" value="{{ old('employee_number') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">القسم <span class="text-red-500">*</span></label>
                                    <select name="department_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm" required>
                                        <option value="">اختر القسم</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">المسمى الوظيفي</label>
                                    <input type="text" name="job_title" value="{{ old('job_title') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                </div>
                            </div>
                        </div>

                        {{-- البيانات الشخصية --}}
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">👤 البيانات الشخصية</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">رقم الهوية/الإقامة</label>
                                    <input type="text" name="national_id" value="{{ old('national_id') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الجنسية</label>
                                    <input type="text" name="nationality" value="{{ old('nationality') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm"
                                        placeholder="مثال: سعودي">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ الميلاد</label>
                                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الجنس</label>
                                    <select name="gender" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                        <option value="">-- اختر --</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة الاجتماعية</label>
                                    <select name="marital_status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                        <option value="">-- اختر --</option>
                                        <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>أعزب</option>
                                        <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>متزوج</option>
                                        <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>مطلق</option>
                                        <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>أرمل</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">رقم الهاتف</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm"
                                        placeholder="05xxxxxxxx">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البريد الإلكتروني</label>
                                    <input type="email" name="email" value="{{ old('email') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                </div>
                            </div>
                        </div>

                        {{-- بيانات الوثائق --}}
                        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">📄 بيانات الوثائق</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">رقم الجواز</label>
                                    <input type="text" name="passport_number" value="{{ old('passport_number') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ انتهاء الجواز</label>
                                    <input type="date" name="passport_expiry" value="{{ old('passport_expiry') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">رقم الإقامة</label>
                                    <input type="text" name="residency_number" value="{{ old('residency_number') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ انتهاء الإقامة</label>
                                    <input type="date" name="residency_expiry" value="{{ old('residency_expiry') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                </div>
                            </div>
                        </div>

                        {{-- بيانات العمل --}}
                        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">💼 بيانات العمل</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ التعيين</label>
                                    <input type="date" name="hire_date" value="{{ old('hire_date') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نوع العقد</label>
                                    <select name="contract_type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                        <option value="">-- اختر --</option>
                                        <option value="permanent" {{ old('contract_type') == 'permanent' ? 'selected' : '' }}>دائم</option>
                                        <option value="temporary" {{ old('contract_type') == 'temporary' ? 'selected' : '' }}>مؤقت</option>
                                        <option value="probation" {{ old('contract_type') == 'probation' ? 'selected' : '' }}>تحت التجربة</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ انتهاء العقد</label>
                                    <input type="date" name="contract_expiry" value="{{ old('contract_expiry') }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                </div>
                            </div>
                        </div>

                        {{-- صورة الموظف --}}
                        <div class="mb-6 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">📷 صورة الموظف</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اختر صورة</label>
                                <input type="file" name="photo" accept="image/*"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">الصيغ المدعومة: JPG, PNG, GIF. الحد الأقصى: 2MB</p>
                            </div>
                        </div>

                        {{-- الحالة --}}
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" 
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm dark:bg-gray-700" checked>
                                <span class="mr-2 text-sm text-gray-700 dark:text-gray-300">مفعّل</span>
                            </label>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                💾 حفظ
                            </button>
                            <a href="{{ route('employees.index') }}" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                إلغاء
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
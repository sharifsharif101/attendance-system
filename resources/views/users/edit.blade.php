<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            تعديل المستخدم: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">الاسم</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                class="w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                class="w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور الجديدة (اتركها فارغة إذا لا تريد التغيير)</label>
                            <input type="password" name="password" 
                                class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation" 
                                class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">الدور</label>
                            <select name="role" class="w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">اختر الدور</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" 
                                        {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">الأقسام</label>
                            <div class="space-y-2">
                                @foreach($departments as $department)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="departments[]" 
                                            value="{{ $department->id }}"
                                            {{ $user->departments->contains($department->id) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 shadow-sm">
                                        <span class="mr-2 text-sm text-gray-700">{{ $department->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                تحديث
                            </button>
                            <a href="{{ route('users.index') }}" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                إلغاء
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="max-w-2xl mx-auto">
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-xl shadow-lg">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-10">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">إضافة مستخدم جديد</h1>
            <p class="text-gray-600 mt-2 text-lg">املأ البيانات التالية لإضافة مستخدم جديد</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-base font-semibold text-gray-700 mb-2">الاسم الكامل *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div class="md:col-span-2">
                    <label for="username" class="block text-base font-semibold text-gray-700 mb-2">اسم المستخدم *</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="md:col-span-2">
                    <label for="email" class="block text-base font-semibold text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-base font-semibold text-gray-700 mb-2">كلمة المرور *</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-base font-semibold text-gray-700 mb-2">تأكيد كلمة المرور *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                </div>

                <!-- Role -->
                <div class="md:col-span-2">
                    <label for="role" class="block text-base font-semibold text-gray-700 mb-2">الدور *</label>
                    <select name="role" id="role" required
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                        <option value="">اختر الدور</option>
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>مستخدم</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>مدير النظام</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 hover:shadow-lg transition-all duration-200 text-sm font-semibold whitespace-nowrap">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-sm font-semibold whitespace-nowrap">
                    إضافة المستخدم
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Clear form fields after successful submission
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            // Clear all input fields
            document.getElementById('name').value = '';
            document.getElementById('username').value = '';
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';
            document.getElementById('role').value = '';
        });
    @endif
</script>
@endpush
@endsection


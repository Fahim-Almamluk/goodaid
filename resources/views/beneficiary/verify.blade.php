<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>التحقق من الهوية - GoodAid</title>
    
    <!-- Google Fonts - Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-emerald-50 to-teal-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-xl shadow-xl border border-emerald-300 p-10">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="flex items-center justify-center space-x-3 space-x-reverse mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">GoodAid</h1>
                    </div>
                </div>
                <p class="text-xl font-semibold text-gray-700">التحقق من الهوية</p>
                <p class="text-sm text-gray-500 mt-2">أدخل رقم الهوية الوطنية للتحقق</p>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-r-4 border-red-500 rounded-xl p-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li class="text-red-700 text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-r-4 border-green-500 rounded-xl p-4">
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('beneficiary.verify') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="national_id" class="block text-sm font-medium text-gray-700 mb-2">رقم الهوية الوطنية</label>
                    <div class="relative">
                        <input type="text" name="national_id" id="national_id" value="{{ old('national_id') }}" 
                            required maxlength="9" pattern="[0-9]{9}"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base text-center text-2xl tracking-widest"
                            placeholder="123456789">
                    </div>
                    <p class="text-xs text-gray-500 mt-2">يجب أن يكون 9 أرقام</p>
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all font-semibold text-base">
                    التحقق
                </button>
            </form>
        </div>
    </div>

    <script>
        // Auto format national ID input
        document.getElementById('national_id')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 9);
        });
    </script>
</body>
</html>


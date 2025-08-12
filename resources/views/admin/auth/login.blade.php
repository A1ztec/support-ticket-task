<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - Support System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body
    class="bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-800 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl p-8 border border-white/20">
            <!-- Header -->
            <div class="text-center mb-8">
                <div
                    class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Admin Panel</h1>
                <p class="text-gray-600 mt-2">Sign in to access the dashboard</p>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-6">
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            <div class="flex-1">
                                @foreach($errors->all() as $error)
                                    <p class="text-red-700 text-sm">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Success Messages -->
            @if(session('success'))
                <div class="mb-6">
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <p class="text-green-700 text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.auth.login.post') }}" class="space-y-6" id="loginForm">
                @csrf

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-envelope mr-1"></i>Email Address
                    </label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('email') border-red-500 bg-red-50 @enderror"
                            placeholder="Enter your email" required autocomplete="email">
                        @error('email')
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                        @enderror
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-lock mr-1"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('password') border-red-500 bg-red-50 @enderror"
                            placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePassword()">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="space-y-4">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        id="submitBtn">
                        <span id="submitText">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </span>
                        <span id="loadingText" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Signing In...
                        </span>
                    </button>
                </div>
            </form>


            <div class="mt-8 text-center text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} Support System. All rights reserved.</p>
            </div>
        </div>


        <div class="text-center mt-6">
            <p class="text-white/70 text-sm">
                <i class="fas fa-info-circle mr-1"></i>
                For admin access only
            </p>
        </div>
    </div>

    <script>

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash text-gray-400 hover:text-gray-600';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye text-gray-400 hover:text-gray-600';
            }
        }


        document.getElementById('loginForm').addEventListener('submit', function () {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingText = document.getElementById('loadingText');

            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            loadingText.classList.remove('hidden');
        });


        setTimeout(function () {
            const errorAlert = document.querySelector('.bg-red-50');
            if (errorAlert) {
                errorAlert.style.transition = 'opacity 0.5s ease-out';
                errorAlert.style.opacity = '0';
                setTimeout(() => errorAlert.remove(), 500);
            }
        }, 5000);


        setTimeout(function () {
            const successAlert = document.querySelector('.bg-green-50');
            if (successAlert) {
                successAlert.style.transition = 'opacity 0.5s ease-out';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 500);
            }
        }, 3000);


        window.addEventListener('load', function () {
            document.getElementById('email').focus();
        });
    </script>
</body>

</html>

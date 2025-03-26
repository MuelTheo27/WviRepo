<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body class="font-[sans-serif] bg-gray-50">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="grid md:grid-cols-2 items-center gap-4 max-w-6xl w-full p-4 m-4 shadow-[0_2px_10px_-3px_rgba(255,165,0,0.3)] rounded-md bg-white">

            <!-- Form Section -->
            <div class="md:max-w-md w-full px-4 py-4">
                @if (session('status'))
                    <div class="mb-4 text-green-600 font-semibold">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-10">
                        <h3 class="text-gray-800 text-3xl font-extrabold">Sign in</h3>
                        <p class="text-sm mt-3 text-gray-700">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-orange-500 font-semibold hover:underline ml-1">
                                Register here
                            </a>
                        </p>
                    </div>

                    <!-- Email -->
                    <div class="mb-8">
                        <label for="email" class="text-gray-800 text-xs block mb-2">Email</label>
                        <div class="relative flex items-center">
                            <input id="email" name="email" type="email" required
                                class="w-full text-gray-800 text-sm border-b border-gray-300 focus:border-orange-500 pl-2 pr-8 py-3 outline-none"
                                placeholder="Enter email" value="{{ old('email') }}" autofocus>
                            @error('email')
                                <span class="text-red-500 text-xs absolute bottom-[-20px]">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-8">
                        <label for="password" class="text-gray-800 text-xs block mb-2">Password</label>
                        <div class="relative flex items-center">
                            <input id="password" name="password" type="password" required
                                class="w-full text-gray-800 text-sm border-b border-gray-300 focus:border-orange-500 pl-2 pr-8 py-3 outline-none"
                                placeholder="Enter password">
                            @error('password')
                                <span class="text-red-500 text-xs absolute bottom-[-20px]">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex justify-between items-center mt-6">
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="h-4 w-4 text-orange-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 text-sm text-gray-800">Remember me</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-orange-500 font-semibold text-sm hover:underline">
                                Forgot Password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-10">
                        <button type="submit"
                            class="w-full py-2.5 px-4 text-sm rounded-md text-white bg-orange-500 hover:bg-orange-600 focus:outline-none">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>

            <!-- Image Section -->
            <div class="hidden md:flex items-center justify-center">
                <img src="https://www.worldvision.org.ph/wp-content/uploads/2024/11/Photo-10-28-24-8-58-37-AM-scaled.jpg" alt="Login Illustration" class="rounded-md">
            </div>
        </div>
    </div>
</body>
</html>

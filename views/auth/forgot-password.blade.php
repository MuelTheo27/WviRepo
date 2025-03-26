<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-orange-50 min-h-screen flex items-center justify-center py-8 px-4 font-[sans-serif]">
  <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-orange-200 p-8">
    <h2 class="text-2xl font-bold text-orange-700 text-center mb-4">Forgot your password?</h2>
    <p class="text-sm text-orange-600 text-center mb-6">
      No problem. Enter your email below and we will send you a password reset link.
    </p>

    <!-- Session Status -->
    @if (session('status'))
    <div class="mb-4 text-green-600 text-sm text-center font-medium">
      {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
      @csrf

      <!-- Email Address -->
      <div>
        <label for="email" class="block text-orange-800 text-sm font-semibold mb-2">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
          placeholder="Enter your email"
          class="w-full text-orange-900 text-sm border border-orange-300 rounded-md px-4 py-3 outline-orange-500 focus:ring-2 focus:ring-orange-400 focus:border-orange-500" />
        @error('email')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
      </div>

      <div class="flex items-center justify-end">
        <button type="submit"
          class="w-full py-3 px-4 text-sm font-semibold rounded-lg text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-400 transition">
          Email Password Reset Link
        </button>
      </div>
    </form>

    <p class="text-sm text-orange-700 text-center mt-6">
      Remember your password?
      <a href="{{ route('login') }}" class="text-orange-600 font-semibold hover:underline">Sign in</a>
    </p>
  </div>
</body>

</html>

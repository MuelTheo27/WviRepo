<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-orange-50 min-h-screen flex items-center justify-center py-8 px-4 font-[sans-serif]">
  <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-orange-200 p-8">
    <!-- Logo (Kotak) -->
    <div class="flex justify-center mb-6">
      <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/World_Vision_new_logo.png/1200px-World_Vision_new_logo.png"
           alt="Logo"
           class="w-28 h-auto shadow-md">
    </div>

    <h2 class="text-2xl font-bold text-orange-700 text-center mb-4">Create an account</h2>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
      @csrf

      <!-- Name -->
      <div>
        <label for="name" class="block text-orange-800 text-sm font-semibold mb-2">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
          placeholder="Enter your name"
          class="w-full text-orange-900 border border-orange-300 px-4 py-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-500" />
        @error('name')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-orange-800 text-sm font-semibold mb-2">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
          placeholder="Enter your email"
          class="w-full text-orange-900 border border-orange-300 px-4 py-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-500" />
        @error('email')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-orange-800 text-sm font-semibold mb-2">Password</label>
        <input id="password" type="password" name="password" required autocomplete="new-password"
          placeholder="Enter your password"
          class="w-full text-orange-900 border border-orange-300 px-4 py-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-500" />
        @error('password')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="password_confirmation" class="block text-orange-800 text-sm font-semibold mb-2">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
          placeholder="Confirm your password"
          class="w-full text-orange-900 border border-orange-300 px-4 py-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-500" />
        @error('password_confirmation')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
      </div>

      <!-- Buttons -->
      <div class="flex items-center justify-between mt-6">
        <a href="{{ route('login') }}"
          class="text-sm text-orange-600 font-semibold hover:underline focus:outline-none focus:ring-2 focus:ring-orange-400">
          Already registered?
        </a>

        <button type="submit"
          class="py-3 px-6 text-sm font-semibold rounded-lg text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-400 transition">
          Register
        </button>
      </div>
    </form>
  </div>
</body>

</html>

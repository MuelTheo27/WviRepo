@yield('content')
<script src="https://cdn.tailwindcss.com"></script>
<header class="bg-white fixed w-full z-20 top-0 border-b shadow-sm">
    <div class="max-w-screen-xl mx-auto flex items-center justify-between p-4">
        <a href="/" class="flex items-center space-x-3">
            <img src="https://wahanavisi.org/themes/front/default/images/global/logo-wvi.webp" class="h-8" alt="Wahana Visi Logo">
        </a>

        <div class="md:flex md:items-center md:space-x-8" id="navbar-sticky">
            <a href="#" class="group text-gray-600 hover:text-gray-400 transition duration-300">
                Home
                <span class="block max-w-0 group-hover:max-w-full transition-all duration-500 h-0.5 bg-gray-600 group-hover:bg-gray-300"></span>
            </a>
            <a href="#" class="group text-gray-600 hover:text-gray-400 transition duration-300">
                Service
                <span class="block max-w-0 group-hover:max-w-full transition-all duration-500 h-0.5 bg-gray-600 group-hover:bg-gray-300"></span>
            </a>
            <a href="#" class="group text-gray-600 hover:text-gray-400 transition duration-300">
                About
                <span class="block max-w-0 group-hover:max-w-full transition-all duration-500 h-0.5 bg-gray-600 group-hover:bg-gray-300"></span>
            </a>
            <a href="#" class="group text-gray-600 hover:text-gray-400 transition duration-300">
                Location
                <span class="block max-w-0 group-hover:max-w-full transition-all duration-500 h-0.5 bg-gray-600 group-hover:bg-gray-300"></span>
            </a>

        </div>

        @if (Route::has('login'))
        <nav class="flex space-x-4">
            @auth
                <a href="{{ url('/dashboard') }}" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-orange-600 text-white px-4 py-2 text-sm font-medium rounded-lg">
                    Log in
                </a>
    
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-orange-600 text-white px-4 py-2 text-sm font-medium rounded-lg">
                        Register
                    </a>
                @endif
            @endauth
        </nav>
    @endif    
    </div>
</header>

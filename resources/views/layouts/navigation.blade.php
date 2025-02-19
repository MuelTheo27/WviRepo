<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Navigation Links -->
        <div class="flex items-center space-x-8">
          <a href="{{ route('') }}" class="text-gray-700 hover:text-gray-900 font-semibold">
            {{ __('Home') }}
          </a>
          <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 font-semibold">
            {{ __('Dashboard') }}
          </a>
        </div>

        <!-- Settings Dropdown -->
        <div class="hidden sm:flex sm:items-center sm:space-x-6">
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white hover:text-gray-700 focus:outline-none">
                <span>{{ Auth::user()->name }}</span>
                <svg class="ml-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                  <path fill="currentColor" fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
                </svg>
              </button>
            </x-slot>

            <x-slot name="content">
              <x-dropdown-link :href="route('profile.edit')">
                {{ __('Profile') }}
              </x-dropdown-link>

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                  {{ __('Log Out') }}
                </x-dropdown-link>
              </form>
            </x-slot>
          </x-dropdown>
        </div>

        <!-- Hamburger Menu (Mobile) -->
        <div class="sm:hidden flex items-center">
          <button @click="open = !open"
            class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <path :class="{'hidden': open, 'block': !open}" class="block" stroke="currentColor" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
              <path :class="{'hidden': !open, 'block': open}" class="hidden" stroke="currentColor" stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="sm:hidden hidden">
      <div class="py-2 space-y-1">
        <a href="{{ route('home') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 font-medium">
          {{ __('Home') }}
        </a>
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 font-medium">
          {{ __('Dashboard') }}
        </a>
      </div>

      <!-- Responsive Settings -->
      <div class="border-t border-gray-200 py-4">
        <div class="px-4">
          <div class="font-medium text-gray-800">{{ Auth::user()->name }}</div>
          <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
        </div>

        <div class="mt-3 space-y-1">
          <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
            {{ __('Profile') }}
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
              onclick="event.preventDefault(); this.closest('form').submit();">
              {{ __('Log Out') }}
            </a>
          </form>
        </div>
      </div>
    </div>
  </nav>

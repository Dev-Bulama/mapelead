<nav x-data="{ open: false, scrolled: false }"
     x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
     :class="scrolled ? 'bg-white shadow-lg' : 'bg-white shadow-sm'"
     class="sticky top-0 z-40 transition-all duration-300">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-20">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                @php $logo = \App\Models\SiteSetting::get('site_logo'); @endphp
                @if($logo)
                    @php $siteName = \App\Models\SiteSetting::get('site_name', 'MapeLearn'); @endphp
                    <img src="{{ asset('storage/'.$logo) }}" alt="{{ $siteName }}" class="h-10 w-auto"
                         onerror="this.style.display='none';document.getElementById('nav-logo-fallback').style.display='flex'">
                    <div id="nav-logo-fallback" class="items-center gap-2" style="display:none">
                        <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($siteName, 0, 2)) }}</span>
                        </div>
                        <span class="text-xl font-display font-bold text-gray-900">{{ $siteName }}</span>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">ML</span>
                        </div>
                        <span class="text-xl font-display font-bold text-gray-900">
                            {{ \App\Models\SiteSetting::get('site_name', 'MapeLearn') }}
                        </span>
                    </div>
                @endif
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden lg:flex items-center gap-8">
                @php
                    $menu = \App\Models\NavigationMenu::where('location', 'header')->where('is_active', true)->with(['items' => fn($q) => $q->where('is_active', true)->whereNull('parent_id')->with(['children' => fn($q) => $q->where('is_active', true)])->orderBy('sort_order')])->first();
                @endphp
                @if($menu)
                    @foreach($menu->items as $item)
                        @if($item->children->count())
                            <div x-data="{ dropdown: false }" class="relative">
                                <button @click="dropdown = !dropdown" @click.away="dropdown = false"
                                        class="flex items-center gap-1 text-gray-700 hover:text-brand-600 font-medium transition-colors">
                                    {{ $item->label }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="dropdown" x-cloak
                                     class="absolute top-full left-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2">
                                    @foreach($item->children as $child)
                                        <a href="{{ $child->resolved_url }}" class="block px-4 py-2 text-gray-700 hover:bg-brand-50 hover:text-brand-600 text-sm">
                                            {{ $child->label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->resolved_url }}"
                               target="{{ $item->target }}"
                               class="text-gray-700 hover:text-brand-600 font-medium transition-colors {{ request()->is(ltrim($item->url, '/')) ? 'text-brand-600' : '' }}">
                                {{ $item->label }}
                            </a>
                        @endif
                    @endforeach
                @endif
            </div>

            {{-- Auth Buttons / User Menu --}}
            <div class="hidden lg:flex items-center gap-3">
                @guest
                    <a href="{{ route('auth.login') }}" class="text-gray-700 hover:text-brand-600 font-medium px-4 py-2 transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('auth.register') }}" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors">
                        Get Started
                    </a>
                @else
                    <div x-data="{ userMenu: false }" class="relative">
                        <button @click="userMenu = !userMenu" @click.away="userMenu = false"
                                class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-xl px-3 py-2 transition-colors">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->full_name }}" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-sm font-medium text-gray-700">{{ auth()->user()->first_name }}</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="userMenu" x-cloak
                             class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-brand-50 hover:text-brand-600 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg> Admin Panel
                                </a>
                            @endif
                            <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-brand-50 hover:text-brand-600 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg> Dashboard
                            </a>
                            <a href="{{ route('student.courses') }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-brand-50 hover:text-brand-600 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg> My Courses
                            </a>
                            <a href="{{ route('student.profile') }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-brand-50 hover:text-brand-600 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Profile
                            </a>
                            <hr class="my-1 border-gray-100">
                            <form action="{{ route('auth.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-red-600 hover:bg-red-50 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>

            {{-- Mobile Menu Toggle --}}
            <button @click="open = !open" class="lg:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-cloak class="lg:hidden border-t border-gray-100 bg-white">
        <div class="px-4 py-4 space-y-1">
            @if(isset($menu))
                @foreach($menu->items as $item)
                    <a href="{{ $item->resolved_url }}" class="block px-3 py-2 text-gray-700 hover:bg-brand-50 hover:text-brand-600 rounded-lg font-medium">
                        {{ $item->label }}
                    </a>
                @endforeach
            @endif
            <hr class="my-2">
            @guest
                <a href="{{ route('auth.login') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Sign In</a>
                <a href="{{ route('auth.register') }}" class="block px-3 py-2 bg-brand-600 text-white rounded-lg font-semibold text-center">Get Started</a>
            @else
                <a href="{{ route('student.dashboard') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Dashboard</a>
                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg">Sign Out</button>
                </form>
            @endguest
        </div>
    </div>
</nav>

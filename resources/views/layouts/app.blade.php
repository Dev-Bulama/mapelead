<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Dynamic SEO Meta --}}
    <title>@yield('title', \App\Models\SiteSetting::get('meta_title', config('app.name')))</title>
    <meta name="description" content="@yield('meta_description', \App\Models\SiteSetting::get('meta_description', ''))">
    <meta name="keywords" content="@yield('meta_keywords', '')">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('title', \App\Models\SiteSetting::get('meta_title', config('app.name')))">
    <meta property="og:description" content="@yield('meta_description', \App\Models\SiteSetting::get('meta_description', ''))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @else
        <meta property="og:image" content="{{ asset('images/og-default.jpg') }}">
    @endif

    {{-- Favicon --}}
    @php $favicon = \App\Models\SiteSetting::get('site_favicon'); @endphp
    @if($favicon)
        <link rel="icon" href="{{ asset('storage/'.$favicon) }}" type="image/x-icon">
    @endif

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eef0f8',
                            100: '#d4d9ef',
                            200: '#a9b3df',
                            300: '#7f8ecf',
                            400: '#5468bf',
                            500: '#2a42af',
                            600: '#14215B',
                            700: '#0f1a48',
                            800: '#0b1335',
                            900: '#070d22',
                            950: '#040812',
                        },
                        accent: {
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js + Intersect plugin (must load before Alpine) --}}
    <script defer src="https://unpkg.com/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    {{-- Head Scripts from CMS --}}
    @php try { $headScripts = \App\Models\ScriptInjection::active()->where('location','head')->get(); } catch(\Exception $e) { $headScripts = collect(); } @endphp
    @foreach($headScripts as $script)
        {!! $script->code !!}
    @endforeach

    <style>
        html, body { overflow-x: hidden; max-width: 100%; }
        [x-cloak] { display: none !important; }
        .gradient-text { background: linear-gradient(135deg, #6366f1, #8b5cf6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-gradient { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #1e1b4b 100%); }
        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(99,102,241,0.15); }
        .progress-bar { transition: width 0.5s ease; }
        .animate-fade-in { animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    @stack('styles')
</head>
<body class="font-sans bg-gray-50 text-gray-900 antialiased">
    {{-- Body Start Scripts from CMS --}}
    @php try { $bodyStartScripts = \App\Models\ScriptInjection::active()->where('location','body_start')->get(); } catch(\Exception $e) { $bodyStartScripts = collect(); } @endphp
    @foreach($bodyStartScripts as $script)
        {!! $script->code !!}
    @endforeach

    {{-- Announcements --}}
    @php
        $announcement = \App\Models\Announcement::where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->first();
    @endphp
    @if($announcement)
        @php $announcementKey = 'dismissed_announcement_' . $announcement->id; @endphp

        @if($announcement->type === 'popup')
        {{-- Popup Modal --}}
        <div x-data="{
                show: !localStorage.getItem('{{ $announcementKey }}')
             }"
             x-show="show" x-cloak
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
             style="background: rgba(0,0,0,0.55);">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 relative"
                 style="border-top: 4px solid {{ $announcement->bg_color }};">
                @if($announcement->is_dismissible)
                <button @click="show = false; localStorage.setItem('{{ $announcementKey }}', '1')"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                @endif
                @if($announcement->title)
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $announcement->title }}</h3>
                @endif
                <p class="text-gray-700 leading-relaxed">{{ $announcement->message }}</p>
                @if($announcement->url)
                <div class="mt-5 flex gap-3 justify-end">
                    <a href="{{ $announcement->url }}"
                       class="px-5 py-2 rounded-lg text-sm font-semibold text-white"
                       style="background-color: {{ $announcement->bg_color }}">
                        {{ $announcement->url_text ?? 'Learn more' }}
                    </a>
                    @if($announcement->is_dismissible)
                    <button @click="show = false; localStorage.setItem('{{ $announcementKey }}', '1')"
                            class="px-5 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                        Dismiss
                    </button>
                    @endif
                </div>
                @elseif($announcement->is_dismissible)
                <div class="mt-5 text-right">
                    <button @click="show = false; localStorage.setItem('{{ $announcementKey }}', '1')"
                            class="px-5 py-2 rounded-lg text-sm font-semibold text-white"
                            style="background-color: {{ $announcement->bg_color }}">
                        Got it
                    </button>
                </div>
                @endif
            </div>
        </div>

        @elseif($announcement->type === 'ticker')
        {{-- Scrolling Ticker --}}
        <div x-data="{ show: true }" x-show="show" x-cloak
             style="background-color: {{ $announcement->bg_color }}; color: {{ $announcement->text_color }};"
             class="py-2 text-sm font-medium relative overflow-hidden">
            <div class="flex items-center gap-4 animate-marquee whitespace-nowrap">
                @for($i = 0; $i < 3; $i++)
                <span>
                    {{ $announcement->message }}
                    @if($announcement->url)
                        &nbsp;—&nbsp;<a href="{{ $announcement->url }}" class="underline font-semibold">{{ $announcement->url_text ?? 'Learn more' }}</a>
                    @endif
                    &nbsp;&nbsp;&nbsp;&nbsp;✦&nbsp;&nbsp;&nbsp;&nbsp;
                </span>
                @endfor
            </div>
            @if($announcement->is_dismissible)
            <button @click="show = false"
                    class="absolute right-4 top-1/2 -translate-y-1/2 opacity-70 hover:opacity-100 z-10 bg-black/10 rounded-full w-6 h-6 flex items-center justify-center text-xs">✕</button>
            @endif
        </div>
        <style>
            @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-33.333%); } }
            .animate-marquee { animation: marquee 18s linear infinite; }
        </style>

        @else
        {{-- Default: Bar --}}
        <div x-data="{ show: true }" x-show="show" x-cloak
             style="background-color: {{ $announcement->bg_color }}; color: {{ $announcement->text_color }};"
             class="text-center py-2 px-4 text-sm font-medium relative">
            {{ $announcement->message }}
            @if($announcement->url)
                <a href="{{ $announcement->url }}" class="underline ml-1">{{ $announcement->url_text ?? 'Learn more' }}</a>
            @endif
            @if($announcement->is_dismissible)
                <button @click="show = false" class="absolute right-4 top-1/2 -translate-y-1/2 opacity-70 hover:opacity-100">✕</button>
            @endif
        </div>
        @endif
    @endif

    {{-- Navigation --}}
    @include('partials.navbar')

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-20 right-4 z-50 animate-fade-in">
            <div class="bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
                <button @click="show = false" class="ml-2 opacity-70 hover:opacity-100">✕</button>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-cloak
             class="fixed top-20 right-4 z-50 animate-fade-in">
            <div class="bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg max-w-sm">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                    <button @click="show = false" class="ml-auto opacity-70 hover:opacity-100">✕</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Body End Scripts from CMS --}}
    @php try { $bodyEndScripts = \App\Models\ScriptInjection::active()->where('location','body_end')->get(); } catch(\Exception $e) { $bodyEndScripts = collect(); } @endphp
    @foreach($bodyEndScripts as $script)
        {!! $script->code !!}
    @endforeach

    @stack('scripts')
</body>
</html>

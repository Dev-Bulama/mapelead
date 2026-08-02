<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sign In') — {{ \App\Models\SiteSetting::get('site_name', 'MapeLeads') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 50:'#eef0f8',100:'#d4d9ef',500:'#2a42af',600:'#14215B',700:'#0f1a48',800:'#0b1335',900:'#070d22',950:'#040812' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans bg-gradient-to-br from-brand-950 via-brand-900 to-slate-900 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            @php $siteLogo = \App\Models\SiteSetting::get('site_logo'); @endphp
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 justify-center">
                @if($siteLogo)
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ \App\Models\SiteSetting::get('site_name', 'MapeLeads') }}" class="h-12 max-w-[200px] object-contain"
                         onerror="this.style.display='none';this.parentNode.querySelector('.auth-logo-fallback').style.display='flex'">
                    <div class="auth-logo-fallback items-center gap-2" style="display:none">
                        <div class="w-10 h-10 bg-brand-500 rounded-xl flex items-center justify-center shrink-0">
                            <span class="text-white font-bold">{{ strtoupper(substr(\App\Models\SiteSetting::get('site_name', 'ML'), 0, 2)) }}</span>
                        </div>
                        <span class="text-white font-bold text-2xl">{{ \App\Models\SiteSetting::get('site_name', 'MapeLeads') }}</span>
                    </div>
                @else
                    <div class="w-10 h-10 bg-brand-500 rounded-xl flex items-center justify-center shrink-0">
                        <span class="text-white font-bold">ML</span>
                    </div>
                    <span class="text-white font-bold text-2xl">{{ \App\Models\SiteSetting::get('site_name', 'MapeLeads') }}</span>
                @endif
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            @yield('content')
        </div>

        {{-- Footer Link --}}
        <p class="text-center text-white/80 text-sm mt-6">
            @yield('footer_link')
        </p>
    </div>
</body>
</html>

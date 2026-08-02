@php
    $general = \App\Models\SiteSetting::getGroup('general');
    $social  = \App\Models\SiteSetting::getGroup('social');
    $footer  = \App\Models\SiteSetting::getGroup('footer');
    $footer1 = \App\Models\NavigationMenu::where('location','footer_1')->with(['items' => fn($q)=>$q->where('is_active',true)->orderBy('sort_order')])->first();
    $footer2 = \App\Models\NavigationMenu::where('location','footer_2')->with(['items' => fn($q)=>$q->where('is_active',true)->orderBy('sort_order')])->first();
    $footer3 = \App\Models\NavigationMenu::where('location','footer_3')->with(['items' => fn($q)=>$q->where('is_active',true)->orderBy('sort_order')])->first();
@endphp

<footer class="bg-gray-900 text-gray-300">
    {{-- Newsletter Band --}}
    <div class="bg-brand-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-white text-xl font-display font-bold">Stay ahead of the curve</h3>
                    <p class="text-brand-200 mt-1">Get weekly insights, course updates & career tips.</p>
                </div>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex gap-2 w-full md:w-auto">
                    @csrf
                    <input type="email" name="email" placeholder="Enter your email" required
                           class="flex-1 md:w-72 px-4 py-3 rounded-xl bg-white text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-white">
                    <button type="submit" class="bg-white text-brand-700 font-semibold px-5 py-3 rounded-xl hover:bg-brand-50 transition-colors whitespace-nowrap">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Main Footer --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">

            {{-- Brand Column --}}
            <div>
                @php
                    $logoPath   = $general['site_logo'] ?? null;
                    $logoHeight = (int) ($footer['footer_logo_height'] ?? 48);
                    $logoHeight = max(24, min(120, $logoHeight));
                @endphp
                <div class="mb-4">
                    @if($logoPath)
                        @php $footerSiteName = $general['site_name'] ?? 'MapeLeads'; @endphp
                        <img src="{{ asset('storage/' . $logoPath) }}"
                             alt="{{ $footerSiteName }}"
                             style="height: {{ $logoHeight }}px; max-width: 200px; width: auto; object-fit: contain;"
                             onerror="this.style.display='none';document.getElementById('footer-logo-fallback').style.display='flex'">
                        <div id="footer-logo-fallback" class="items-center gap-2" style="display:none">
                            <div class="w-8 h-8 bg-brand-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ strtoupper(substr($footerSiteName, 0, 2)) }}</span>
                            </div>
                            <span class="text-white font-display font-bold text-xl">{{ $footerSiteName }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-brand-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">ML</span>
                            </div>
                            <span class="text-white font-display font-bold text-xl">{{ $general['site_name'] ?? 'MapeLeads' }}</span>
                        </div>
                    @endif
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">
                    {{ $footer['footer_about'] ?? 'Empowering African professionals with world-class tech skills.' }}
                </p>
                {{-- Social Links --}}
                <div class="flex gap-3 mt-5">
                    @foreach([
                        'facebook'  => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z',
                        'twitter'   => 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z',
                        'instagram' => 'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01M6.5 20.5h11a4 4 0 004-4v-11a4 4 0 00-4-4h-11a4 4 0 00-4 4v11a4 4 0 004 4z',
                        'linkedin'  => 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z',
                        'youtube'   => 'M22.54 6.42a2.78 2.78 0 00-1.95-1.95C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 001.46 6.42 29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.95C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z',
                    ] as $network => $path)
                        @if(!empty($social[$network . '_url']))
                            <a href="{{ $social[$network . '_url'] }}" target="_blank" rel="noopener"
                               class="w-9 h-9 bg-gray-800 hover:bg-brand-600 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                                </svg>
                            </a>
                        @endif
                    @endforeach
                    @if(!empty($social['whatsapp_number']))
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $social['whatsapp_number']) }}" target="_blank"
                           class="w-9 h-9 bg-gray-800 hover:bg-green-600 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Footer Menu Columns --}}
            @foreach([
                [
                    'menu'     => $footer1,
                    'label'    => 'Quick Links',
                    'fallback' => [['Home', '/'], ['Courses', '/courses'], ['About Us', '/about'], ['Scholarships', '/scholarships'], ['Gallery', '/gallery'], ['Contact', '/contact']],
                ],
                [
                    'menu'     => $footer2,
                    'label'    => 'Courses',
                    'fallback' => [['All Courses', '/courses'], ['Online Courses', '/courses?type=online'], ['Free Courses', '/courses?free=1'], ['Certificates', '/courses']],
                ],
                [
                    'menu'     => $footer3,
                    'label'    => 'Company',
                    'fallback' => [['About Us', '/about'], ['Why MapeLeads', '/why-us'], ['Hire From Us', '/hire-from-us'], ['Blog', '/blog'], ['Privacy Policy', '/privacy-policy'], ['Terms of Service', '/terms-of-service']],
                ],
            ] as $col)
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ $col['label'] }}</h4>
                    <ul class="space-y-2">
                        @if($col['menu'] && $col['menu']->items->isNotEmpty())
                            @foreach($col['menu']->items as $item)
                                <li>
                                    <a href="{{ $item->resolved_url }}" class="text-gray-400 hover:text-white text-sm transition-colors">
                                        {{ $item->label }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            @foreach($col['fallback'] as [$label, $url])
                                <li><a href="{{ $url }}" class="text-gray-400 hover:text-white text-sm transition-colors">{{ $label }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-gray-500">
            <p>{{ $footer['footer_copyright'] ?? '© ' . date('Y') . ' MapeLeads. All rights reserved.' }}</p>
            <div class="flex gap-4">
                <a href="/privacy-policy" class="hover:text-gray-300 transition-colors">Privacy Policy</a>
                <a href="/terms-of-service" class="hover:text-gray-300 transition-colors">Terms of Service</a>
                <a href="/refund-policy" class="hover:text-gray-300 transition-colors">Refund Policy</a>
            </div>
        </div>
    </div>
</footer>

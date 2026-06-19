@extends('layouts.app')
@section('title', 'About Us — MapeLearn')
@section('meta_description', 'Learn about MapeLearn, Africa\'s premier tech training platform. Discover our mission, team, and impact.')

@section('content')

{{-- Hero --}}
<div class="hero-gradient text-white py-20">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <span class="inline-block bg-white/10 text-white/80 text-sm font-medium px-4 py-1.5 rounded-full mb-4">Our Story</span>
        <h1 class="text-4xl lg:text-5xl font-display font-bold">Empowering Africa's <span class="gradient-text">Digital Future</span></h1>
        <p class="text-brand-200 mt-5 text-xl max-w-2xl mx-auto leading-relaxed">
            We started with a simple belief: world-class tech education should be accessible to every African professional, regardless of where they are.
        </p>
    </div>
</div>

{{-- Mission & Vision --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
        @foreach([
            ['title' => 'Our Mission', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'brand', 'text' => 'To democratize access to premium tech education across Africa, bridging the digital skills gap through practical, industry-relevant training that transforms careers and builds futures.'],
            ['title' => 'Our Vision', 'icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.82V15.18a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color' => 'purple', 'text' => 'To be the leading tech talent development platform in Africa, recognized globally for producing job-ready professionals who drive innovation and economic growth across the continent.'],
        ] as $item)
            <div class="bg-white rounded-2xl border border-gray-100 p-8">
                <div class="w-12 h-12 bg-{{ $item['color'] === 'brand' ? 'brand' : 'purple' }}-100 rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-{{ $item['color'] === 'brand' ? 'brand-600' : 'purple-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $item['title'] }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ $item['text'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Stats --}}
    <div class="bg-brand-950 rounded-3xl p-10 mb-16 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        @foreach([
            ['value' => '10,000+', 'label' => 'Students Trained'],
            ['value' => '50+', 'label' => 'Expert Courses'],
            ['value' => '92%', 'label' => 'Job Placement'],
            ['value' => '25+', 'label' => 'Industry Instructors'],
        ] as $stat)
            <div>
                <p class="text-4xl font-display font-bold text-white">{{ $stat['value'] }}</p>
                <p class="text-brand-300 mt-1 text-sm">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Values --}}
    <div class="text-center mb-10">
        <h2 class="text-3xl font-display font-bold text-gray-900">Our Core Values</h2>
        <p class="text-gray-500 mt-2">The principles that guide everything we do</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
        @foreach([
            ['title' => 'Excellence', 'icon' => '⭐', 'text' => 'We hold ourselves to the highest standards in curriculum design, instruction quality, and student support.'],
            ['title' => 'Practical Learning', 'icon' => '🔧', 'text' => 'Theory is foundation, but real skills come from building real projects with real tools used in the industry.'],
            ['title' => 'Inclusivity', 'icon' => '🌍', 'text' => 'Tech education for all — regardless of background, location, or prior experience. Everyone deserves a chance.'],
            ['title' => 'Integrity', 'icon' => '🤝', 'text' => 'Honest communication, transparent pricing, and genuine commitment to every student\'s success.'],
            ['title' => 'Innovation', 'icon' => '💡', 'text' => 'We continuously evolve our curriculum to match the rapidly changing demands of the tech industry.'],
            ['title' => 'Community', 'icon' => '👥', 'text' => 'Learning is better together. We build networks that last far beyond graduation day.'],
        ] as $value)
            <div class="bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <span class="text-3xl">{{ $value['icon'] }}</span>
                <h3 class="font-bold text-gray-900 mt-3 mb-2">{{ $value['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $value['text'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Meet Our Team --}}
    @if(isset($teamMembers) && $teamMembers->isNotEmpty())
    <div class="mb-16">
        <div class="text-center mb-10">
            <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">Our People</span>
            <h2 class="text-3xl font-display font-bold text-gray-900 mb-2">Meet Our Team</h2>
            <p class="text-gray-500">Industry practitioners and career coaches dedicated to your success.</p>
        </div>

        {{-- Desktop Grid --}}
        <div class="hidden sm:grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($teamMembers as $member)
            <div class="group text-center">
                <div class="relative mb-4 overflow-hidden rounded-2xl aspect-square bg-gray-200">
                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @if($member->linkedin_url || $member->twitter_url)
                    <div class="absolute inset-0 bg-[#14215B]/80 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                        @if($member->linkedin_url)
                        <a href="{{ $member->linkedin_url }}" target="_blank" rel="noopener"
                           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center hover:bg-blue-50 transition-colors">
                            <svg class="w-5 h-5 text-blue-700 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                        @endif
                        @if($member->twitter_url)
                        <a href="{{ $member->twitter_url }}" target="_blank" rel="noopener"
                           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center hover:bg-sky-50 transition-colors">
                            <svg class="w-5 h-5 text-sky-500 fill-current" viewBox="0 0 24 24"><path d="M23.954 4.569a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.691 8.094 4.066 6.13 1.64 3.161a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.061a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.937 4.937 0 004.604 3.417 9.868 9.868 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.054 0 13.999-7.496 13.999-13.986 0-.209 0-.42-.015-.63a9.936 9.936 0 002.46-2.548l-.047-.02z"/></svg>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
                <h3 class="font-display font-bold text-gray-900 text-sm mb-0.5">{{ $member->name }}</h3>
                <p class="text-brand-600 text-xs font-medium">{{ $member->position }}</p>
                @if($member->department)
                <p class="text-gray-400 text-xs mt-0.5">{{ $member->department }}</p>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Mobile Carousel --}}
        <div class="sm:hidden" x-data="{ active: 0, total: {{ $teamMembers->count() }} }">
            <div class="overflow-hidden">
                @foreach($teamMembers as $i => $member)
                <div x-show="active === {{ $i }}" class="text-center px-4">
                    <div class="relative mb-4 overflow-hidden rounded-2xl w-48 h-48 mx-auto bg-gray-200">
                        <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-display font-bold text-gray-900 mb-1">{{ $member->name }}</h3>
                    <p class="text-brand-600 text-sm font-medium">{{ $member->position }}</p>
                    @if($member->department)
                    <p class="text-gray-400 text-xs mt-0.5">{{ $member->department }}</p>
                    @endif
                    @if(isset($member->bio) && $member->bio)
                    <p class="text-gray-500 text-sm mt-3 leading-relaxed">{{ Str::limit($member->bio, 120) }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-center gap-4 mt-6">
                <button @click="active = (active - 1 + total) % total"
                        class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center hover:bg-brand-50 hover:border-brand-300 transition-colors">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="flex gap-2">
                    @foreach($teamMembers as $i => $member)
                    <button @click="active = {{ $i }}" :class="active === {{ $i }} ? 'bg-brand-600 w-6' : 'bg-gray-300 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
                    @endforeach
                </div>
                <button @click="active = (active + 1) % total"
                        class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center hover:bg-brand-50 hover:border-brand-300 transition-colors">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Testimonials --}}
    @if($testimonials->isNotEmpty())
        <div class="text-center mb-10">
            <h2 class="text-3xl font-display font-bold text-gray-900">What Our Students Say</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($testimonials as $testimonial)
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 leading-relaxed">"{{ $testimonial->content }}"</p>
                    <div class="flex items-center gap-3 mt-4">
                        @if($testimonial->avatar)
                            <img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold">{{ substr($testimonial->name, 0, 1) }}</div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $testimonial->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $testimonial->title }}{{ $testimonial->company ? ', ' . $testimonial->company : '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- CTA --}}
    <div class="mt-16 bg-gradient-to-r from-brand-600 to-purple-600 rounded-3xl p-10 text-center text-white">
        <h2 class="text-3xl font-display font-bold">Ready to Start Your Journey?</h2>
        <p class="text-brand-100 mt-3 mb-6">Join thousands of professionals who've already transformed their careers.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('courses.index') }}" class="bg-white text-brand-700 font-bold px-8 py-3.5 rounded-xl hover:bg-brand-50 transition-colors">
                Browse Courses
            </a>
            <a href="{{ route('contact') }}" class="border-2 border-white/40 text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-white/10 transition-colors">
                Contact Us
            </a>
        </div>
    </div>
</div>
@endsection

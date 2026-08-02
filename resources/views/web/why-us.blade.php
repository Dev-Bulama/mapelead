@extends('layouts.app')
@section('title', ($content['why_title'] ?? 'Why MapeLeads') . ' — ' . \App\Models\SiteSetting::get('site_name', 'MapeLeads'))
@section('meta_description', 'Discover why thousands of students choose MapeLeads for their tech education journey — practical training, expert instructors, and proven career outcomes.')

@section('content')

@php
    $title      = $content['why_title']    ?? 'Why Choose MapeLeads?';
    $subtitle   = $content['why_subtitle'] ?? 'More than a tech school — a launchpad for your career';
    $intro      = $content['why_intro']    ?? 'We built MapeLeads because we believe access to quality tech education shouldn\'t depend on your postcode or your pocket. Here\'s what sets us apart.';
    $reasons    = $content['why_reasons']  ?? '';
    $ctaText    = $content['why_cta_text'] ?? 'Explore Our Courses';
    $ctaUrl     = $content['why_cta_url']  ?? '/courses';
    $siteName   = \App\Models\SiteSetting::get('site_name', 'MapeLeads');
@endphp

{{-- Hero --}}
<div class="bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800 text-white py-20">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <span class="inline-block text-3xl mb-4">🚀</span>
        <h1 class="text-4xl lg:text-5xl font-display font-bold mb-4">{{ $title }}</h1>
        <p class="text-brand-200 text-xl max-w-2xl mx-auto">{{ $subtitle }}</p>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">

    {{-- Intro --}}
    <div class="text-center max-w-3xl mx-auto">
        <p class="text-gray-600 text-lg leading-relaxed">{{ $intro }}</p>
    </div>

    {{-- Key Reasons --}}
    @if($reasons)
    <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed">
        {!! $reasons !!}
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach([
            ['icon' => '🧑‍🏫', 'title' => 'Expert Industry Instructors',   'desc' => 'Learn from practitioners — not just academics. Our instructors work in the industry and bring real-world context to every lesson.'],
            ['icon' => '💻', 'title' => 'Practical, Project-Based Learning','desc' => 'No passive lectures. You build real projects from day one, ensuring you have a portfolio that speaks for itself.'],
            ['icon' => '📅', 'title' => 'Flexible Schedules',               'desc' => 'Evening, weekend, and hybrid options so you can learn without quitting your job or disrupting your life.'],
            ['icon' => '🎓', 'title' => 'Recognised Certification',         'desc' => 'Our certificates are trusted by employers across Nigeria and internationally, backing your skillset with credibility.'],
            ['icon' => '💳', 'title' => 'Affordable & Flexible Payment',    'desc' => 'Competitive tuition with instalment plans and scholarship options to remove financial barriers.'],
            ['icon' => '🌐', 'title' => 'Career Support & Job Placement',   'desc' => 'CV reviews, interview prep, LinkedIn optimisation, and direct connections to our employer network.'],
        ] as $reason)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-start gap-4">
            <div class="w-12 h-12 bg-brand-50 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">{{ $reason['icon'] }}</div>
            <div>
                <h3 class="font-display font-semibold text-gray-900 text-lg mb-1">{{ $reason['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $reason['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Testimonials --}}
    @if($testimonials->isNotEmpty())
    <div>
        <h2 class="text-2xl font-display font-bold text-gray-900 mb-8 text-center">What Our Students Say</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($testimonials as $testimonial)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
                <div class="flex items-center gap-1 mb-3">
                    @for($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4 {{ $i < ($testimonial->rating ?? 5) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed flex-grow">"{{ $testimonial->content }}"</p>
                <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-50">
                    @if($testimonial->avatar)
                        <img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->name }}" class="w-9 h-9 rounded-full object-cover">
                    @else
                        <div class="w-9 h-9 bg-brand-100 rounded-full flex items-center justify-center text-brand-700 font-semibold text-sm">{{ substr($testimonial->name, 0, 1) }}</div>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $testimonial->name }}</p>
                        @if($testimonial->role || $testimonial->company)
                        <p class="text-xs text-gray-500">{{ $testimonial->role }}{{ $testimonial->role && $testimonial->company ? ', ' : '' }}{{ $testimonial->company }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Stats --}}
    <div class="bg-brand-50 rounded-2xl border border-brand-100 p-8">
        <h2 class="text-2xl font-display font-bold text-gray-900 mb-6 text-center">Our Numbers</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            @foreach([
                ['value' => \App\Models\SiteSetting::get('stat_students', '10,000+'),  'label' => \App\Models\SiteSetting::get('stat_students_label', 'Students Trained')],
                ['value' => \App\Models\SiteSetting::get('stat_courses', '50+'),       'label' => \App\Models\SiteSetting::get('stat_courses_label', 'Expert Courses')],
                ['value' => \App\Models\SiteSetting::get('stat_instructors', '25+'),   'label' => \App\Models\SiteSetting::get('stat_instructors_label', 'Industry Instructors')],
                ['value' => \App\Models\SiteSetting::get('stat_placement', '92%'),     'label' => \App\Models\SiteSetting::get('stat_placement_label', 'Job Placement Rate')],
            ] as $stat)
            <div>
                <p class="font-display font-bold text-3xl text-brand-800">{{ $stat['value'] }}</p>
                <p class="text-brand-600 text-sm mt-1">{{ $stat['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- CTA --}}
    <div class="bg-brand-950 rounded-3xl p-10 text-center text-white">
        <h2 class="text-3xl font-display font-bold mb-3">Ready to Start Your Tech Journey?</h2>
        <p class="text-brand-300 mb-8 max-w-xl mx-auto">Join thousands of students who chose {{ $siteName }} to build real skills and launch meaningful careers in tech.</p>
        <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-white text-brand-900 font-semibold px-8 py-4 rounded-xl hover:bg-brand-50 transition-colors text-lg">
            {{ $ctaText }}
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>

</div>
@endsection

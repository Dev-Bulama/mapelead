@extends('layouts.app')
@section('title', ($content['hire_title'] ?? 'Hire From Us') . ' — ' . \App\Models\SiteSetting::get('site_name', 'MapeLeads'))
@section('meta_description', 'Hire skilled tech professionals trained and certified by MapeLeads. Access a pipeline of job-ready graduates across software development, data, design, and more.')

@section('content')

@php
    $title      = $content['hire_title']    ?? 'Hire From MapeLeads';
    $subtitle   = $content['hire_subtitle'] ?? 'Talent-ready tech professionals, trained to industry standards';
    $intro      = $content['hire_intro']    ?? 'Our graduates are rigorously trained across the most in-demand tech disciplines. Whether you need a developer, data analyst, UX designer, or cybersecurity specialist, MapeLeads connects you with motivated, job-ready talent.';
    $whyHire    = $content['hire_why']      ?? '';
    $howItWorks = $content['hire_how_it_works'] ?? '';
    $ctaText    = $content['hire_cta_text'] ?? 'Get in Touch';
    $ctaUrl     = $content['hire_cta_url']  ?? '/contact';
    $siteName   = \App\Models\SiteSetting::get('site_name', 'MapeLeads');
@endphp

{{-- Hero --}}
<div class="bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800 text-white py-20">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <span class="inline-block text-3xl mb-4">🤝</span>
        <h1 class="text-4xl lg:text-5xl font-display font-bold mb-4">{{ $title }}</h1>
        <p class="text-brand-200 text-xl max-w-2xl mx-auto">{{ $subtitle }}</p>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">

    {{-- Intro --}}
    <div class="text-center max-w-3xl mx-auto">
        <p class="text-gray-600 text-lg leading-relaxed">{{ $intro }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @foreach([
            ['value' => '500+', 'label' => 'Graduates placed in tech roles', 'icon' => '👩‍💻'],
            ['value' => '6–12 mo', 'label' => 'Intensive training programmes', 'icon' => '📚'],
            ['value' => '92%', 'label' => 'Graduate employment rate', 'icon' => '📈'],
        ] as $stat)
        <div class="bg-brand-50 rounded-2xl p-6 text-center border border-brand-100">
            <div class="text-3xl mb-2">{{ $stat['icon'] }}</div>
            <p class="font-display font-bold text-2xl text-brand-800">{{ $stat['value'] }}</p>
            <p class="text-brand-600 text-sm mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Why Hire From Us --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-xl">⭐</div>
            <h2 class="text-2xl font-display font-bold text-gray-900">Why Hire From Us?</h2>
        </div>
        @if($whyHire)
            <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed">
                {!! $whyHire !!}
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600">
                @foreach([
                    ['icon' => '🎯', 'title' => 'Industry-aligned curriculum',     'desc' => 'Courses co-designed with employers to match real workplace requirements.'],
                    ['icon' => '🛠',  'title' => 'Hands-on project experience',    'desc' => 'Graduates have built real-world projects and worked in simulated team environments.'],
                    ['icon' => '✅',  'title' => 'Verified skills & certifications','desc' => 'Every graduate holds a {{ $siteName }} certificate backed by rigorous assessments.'],
                    ['icon' => '🌍',  'title' => 'Diverse talent pool',            'desc' => 'Access candidates across Nigeria and Africa, including remote-first professionals.'],
                ] as $item)
                <div class="flex items-start gap-3 bg-gray-50 rounded-xl p-4">
                    <span class="text-2xl">{{ $item['icon'] }}</span>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $item['title'] }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $item['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Available Skills --}}
    <div>
        <h2 class="text-2xl font-display font-bold text-gray-900 mb-6 text-center">Available Skill Sets</h2>
        <div class="flex flex-wrap justify-center gap-3">
            @foreach([
                'Web Development', 'Mobile App Development', 'Data Analysis', 'UI/UX Design',
                'Cybersecurity', 'Cloud Computing', 'DevOps', 'Digital Marketing',
                'Product Management', 'Machine Learning', 'Python Programming', 'JavaScript / React',
            ] as $skill)
            <span class="bg-brand-50 border border-brand-200 text-brand-700 text-sm font-medium px-4 py-2 rounded-full">{{ $skill }}</span>
            @endforeach
        </div>
    </div>

    {{-- How It Works --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-xl">🔄</div>
            <h2 class="text-2xl font-display font-bold text-gray-900">How It Works</h2>
        </div>
        @if($howItWorks)
            <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed">
                {!! $howItWorks !!}
            </div>
        @else
            <ol class="space-y-4 text-gray-600">
                @foreach([
                    ['step' => '1', 'title' => 'Tell Us Your Needs',       'desc' => 'Reach out via the contact form below, describing the role(s), skills, and timeline you need.'],
                    ['step' => '2', 'title' => 'We Match Candidates',      'desc' => 'Our talent team curates a shortlist from our pool of certified graduates aligned to your requirements.'],
                    ['step' => '3', 'title' => 'Interview & Select',       'desc' => 'You interview shortlisted candidates and make your selection — no recruitment fees, no hidden costs.'],
                    ['step' => '4', 'title' => 'Onboard With Confidence',  'desc' => 'We provide post-placement support during the first 30 days to ensure a smooth transition.'],
                ] as $item)
                <li class="flex items-start gap-4">
                    <span class="flex-shrink-0 w-8 h-8 bg-brand-600 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ $item['step'] }}</span>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $item['title'] }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $item['desc'] }}</p>
                    </div>
                </li>
                @endforeach
            </ol>
        @endif
    </div>

    {{-- CTA --}}
    <div class="bg-brand-950 rounded-3xl p-10 text-center text-white">
        <h2 class="text-3xl font-display font-bold mb-3">Ready to Hire Top Tech Talent?</h2>
        <p class="text-brand-300 mb-8 max-w-xl mx-auto">Tell us what you're looking for and we'll connect you with the right candidates from our graduate pool.</p>
        <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-white text-brand-900 font-semibold px-8 py-4 rounded-xl hover:bg-brand-50 transition-colors text-lg">
            {{ $ctaText }}
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>

</div>
@endsection

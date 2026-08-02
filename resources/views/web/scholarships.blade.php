@extends('layouts.app')
@section('title', ($content['scholarships_title'] ?? 'Scholarships') . ' — ' . \App\Models\SiteSetting::get('site_name', 'MapeLeads'))
@section('meta_description', 'Learn about scholarships and financial aid available at MapeLeads to make quality tech education accessible to everyone.')

@section('content')

@php
    $title      = $content['scholarships_title']    ?? 'Scholarships & Financial Aid';
    $subtitle   = $content['scholarships_subtitle'] ?? 'Making quality tech education accessible to all';
    $intro      = $content['scholarships_intro']    ?? 'At MapeLeads, we believe financial barriers should never stand in the way of a world-class tech education. Our scholarship programme is designed to open doors for talented, driven individuals who need a helping hand.';
    $eligibility     = $content['scholarships_eligibility']    ?? '';
    $howToApply      = $content['scholarships_how_to_apply']   ?? '';
    $ctaText         = $content['scholarships_cta_text']       ?? 'Apply Now';
    $ctaUrl          = $content['scholarships_cta_url']        ?? '/contact';
    $siteName        = \App\Models\SiteSetting::get('site_name', 'MapeLeads');
@endphp

{{-- Hero --}}
<div class="bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800 text-white py-20">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <span class="inline-block text-3xl mb-4">🎓</span>
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
            ['value' => '100%', 'label' => 'Free tuition for recipients', 'icon' => '🆓'],
            ['value' => 'Merit &amp; Need', 'label' => 'Based scholarship awards', 'icon' => '⚖️'],
            ['value' => 'Rolling', 'label' => 'Intake — apply anytime', 'icon' => '📅'],
        ] as $stat)
        <div class="bg-brand-50 rounded-2xl p-6 text-center border border-brand-100">
            <div class="text-3xl mb-2">{{ $stat['icon'] }}</div>
            <p class="font-display font-bold text-2xl text-brand-800">{!! $stat['value'] !!}</p>
            <p class="text-brand-600 text-sm mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Eligibility --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center text-xl">📋</div>
            <h2 class="text-2xl font-display font-bold text-gray-900">Eligibility Criteria</h2>
        </div>
        @if($eligibility)
            <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed">
                {!! $eligibility !!}
            </div>
        @else
            <ul class="space-y-3 text-gray-600">
                @foreach([
                    'Must be a Nigerian or African resident',
                    'Demonstrated financial need or exceptional merit',
                    'Clear commitment to a career in tech',
                    'Completed at least secondary education (SSCE/WAEC or equivalent)',
                    'Available to attend classes (online or physical) for the full course duration',
                ] as $item)
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $item }}
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- How To Apply --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-xl">📝</div>
            <h2 class="text-2xl font-display font-bold text-gray-900">How to Apply</h2>
        </div>
        @if($howToApply)
            <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed">
                {!! $howToApply !!}
            </div>
        @else
            <ol class="space-y-4 text-gray-600">
                @foreach([
                    ['step' => '1', 'title' => 'Submit Your Application', 'desc' => 'Click "Apply Now" below and fill in the contact form. Select "Scholarship Enquiry" as the subject.'],
                    ['step' => '2', 'title' => 'Review Process',          'desc' => 'Our admissions team reviews your application within 5–7 business days.'],
                    ['step' => '3', 'title' => 'Interview (if shortlisted)', 'desc' => 'Shortlisted candidates are invited for a brief interview to discuss their goals and circumstances.'],
                    ['step' => '4', 'title' => 'Award Notification',      'desc' => 'Successful applicants receive a scholarship letter and enrolment instructions by email.'],
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
        <h2 class="text-3xl font-display font-bold mb-3">Ready to Apply?</h2>
        <p class="text-brand-300 mb-8 max-w-xl mx-auto">Don't let finances hold you back. Take the first step towards your tech career today.</p>
        <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-white text-brand-900 font-semibold px-8 py-4 rounded-xl hover:bg-brand-50 transition-colors text-lg">
            {{ $ctaText }}
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>

</div>
@endsection

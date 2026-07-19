@extends('layouts.app')
@section('title', 'Contact Us')
@section('meta_description', 'Get in touch with MapeLearn. We are here to help you with your tech training journey.')

@section('content')

{{-- Hero --}}
<div class="bg-brand-950 text-white py-14">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-display font-bold">Get In Touch</h1>
        <p class="text-brand-200 mt-3">Have a question? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        {{-- Contact Info --}}
        <div class="space-y-6">
            <h2 class="text-xl font-bold text-gray-900">Contact Information</h2>

            @php
                $general = \App\Models\SiteSetting::getGroup('general');
                $social  = \App\Models\SiteSetting::getGroup('social');
            @endphp

            @foreach([
                ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Email', 'value' => $general['site_email'] ?? 'hello@mapelead.org'],
                ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => 'Phone', 'value' => $general['site_phone'] ?? '+234 800 000 0000'],
                ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Location', 'value' => $general['site_address'] ?? 'Lagos, Nigeria'],
            ] as $info)
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-brand-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $info['label'] }}</p>
                        <p class="text-gray-900 font-medium mt-0.5">{{ $info['value'] }}</p>
                    </div>
                </div>
            @endforeach

            <div class="bg-brand-50 rounded-2xl p-5 mt-6">
                <h3 class="font-semibold text-brand-900 mb-3">Office Hours</h3>
                <div class="space-y-1.5 text-sm text-brand-700">
                    <div class="flex justify-between"><span>Monday – Friday</span><span class="font-medium">8:00 AM – 6:00 PM</span></div>
                    <div class="flex justify-between"><span>Saturday</span><span class="font-medium">9:00 AM – 3:00 PM</span></div>
                    <div class="flex justify-between"><span>Sunday</span><span class="font-medium text-gray-400">Closed</span></div>
                </div>
            </div>

            @if(!empty($social['whatsapp_number']))
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $social['whatsapp_number']) }}" target="_blank"
                   class="flex items-center gap-3 bg-green-500 hover:bg-green-600 text-white font-semibold px-5 py-3 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Chat on WhatsApp
                </a>
            @endif
        </div>

        {{-- Contact Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Send a Message</h2>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 mb-6">{{ session('success') }}</div>
                @endif

                @php
                    $btnText         = \App\Models\SiteSetting::get('contact_btn_text', 'Send Message');
                    $captchaEnabled  = (bool) \App\Models\SiteSetting::get('recaptcha_enabled', false);
                    $captchaSiteKey  = \App\Models\SiteSetting::get('recaptcha_site_key', '');
                @endphp

                @if($captchaEnabled && $captchaSiteKey)
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" x-data="{ loading: false }" @submit="loading = true" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <select name="subject" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">Select a subject</option>
                                @foreach(['Course Enquiry','Enrollment Support','Payment Issues','Technical Support','Partnership','General Enquiry'] as $subj)
                                    <option value="{{ $subj }}" {{ old('subject') === $subj ? 'selected' : '' }}>{{ $subj }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" rows="5" required
                                  class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                                  placeholder="Tell us how we can help...">{{ old('message') }}</textarea>
                        @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    @if($captchaEnabled && $captchaSiteKey)
                    <div>
                        <div class="g-recaptcha" data-sitekey="{{ $captchaSiteKey }}"></div>
                        @error('g-recaptcha-response')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    @endif

                    <button type="submit" :disabled="loading"
                            class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-8 py-3 rounded-xl transition-colors flex items-center gap-2 disabled:opacity-70">
                        <svg x-show="loading" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        <span x-text="loading ? 'Sending...' : '{{ addslashes($btnText) }}'">{{ $btnText }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Certificate Settings')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.certificates.index') }}" class="text-brand-600 hover:underline text-sm">← Back to Certificates</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Certificate Design Settings</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.certificates.settings.save') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-xl border p-6 mb-4">
            <h2 class="font-semibold text-gray-900 mb-4">Organization & Signatories</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Organization Name</label>
                    <input type="text" name="organization_name" value="{{ old('organization_name', $settings->organization_name) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Mapelead Technology Academy">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Signatory Name</label>
                    <input type="text" name="signatory_name" value="{{ old('signatory_name', $settings->signatory_name) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Signatory Title</label>
                    <input type="text" name="signatory_title" value="{{ old('signatory_title', $settings->signatory_title) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Executive Director">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6 mb-4">
            <h2 class="font-semibold text-gray-900 mb-4">Certificate Text</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Header Text</label>
                    <input type="text" name="header_text" value="{{ old('header_text', $settings->header_text) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Body Text</label>
                    <textarea name="body_text" rows="4" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('body_text', $settings->body_text) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Use {student_name} and {course_name} as placeholders.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Footer Text</label>
                    <input type="text" name="footer_text" value="{{ old('footer_text', $settings->footer_text) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6 mb-4">
            <h2 class="font-semibold text-gray-900 mb-4">Design & Colors</h2>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" name="background_color" value="{{ $settings->background_color ?? '#ffffff' }}" class="h-9 w-14 border rounded cursor-pointer">
                        <input type="text" value="{{ $settings->background_color ?? '#ffffff' }}" class="flex-1 border rounded-lg px-2 py-2 text-sm font-mono" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" name="primary_color" value="{{ $settings->primary_color ?? '#14215B' }}" class="h-9 w-14 border rounded cursor-pointer">
                        <input type="text" value="{{ $settings->primary_color ?? '#14215B' }}" class="flex-1 border rounded-lg px-2 py-2 text-sm font-mono" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text Color</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" name="text_color" value="{{ $settings->text_color ?? '#1a1a1a' }}" class="h-9 w-14 border rounded cursor-pointer">
                        <input type="text" value="{{ $settings->text_color ?? '#1a1a1a' }}" class="flex-1 border rounded-lg px-2 py-2 text-sm font-mono" readonly>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Layout</label>
                    <select name="layout" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="landscape" {{ ($settings->layout ?? 'landscape') === 'landscape' ? 'selected' : '' }}>Landscape (A4)</option>
                        <option value="portrait" {{ ($settings->layout ?? '') === 'portrait' ? 'selected' : '' }}>Portrait (A4)</option>
                    </select>
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="qr_enabled" value="1" {{ $settings->qr_enabled ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-brand-600">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Enable QR Code</span>
                            <p class="text-xs text-gray-400">For certificate verification</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6 mb-4">
            <h2 class="font-semibold text-gray-900 mb-4">Branding Assets</h2>
            <div class="grid grid-cols-3 gap-4">
                @foreach(['logo_path' => ['Logo', $settings->logo_path], 'signature_path' => ['Signature', $settings->signature_path], 'seal_path' => ['Seal/Stamp', $settings->seal_path]] as $field => [$label, $current])
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                    @if($current)
                        <img src="{{ Storage::url($current) }}" alt="{{ $label }}" class="h-16 mb-2 object-contain border rounded">
                    @endif
                    <input type="file" name="{{ $field }}" accept="image/*" class="w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    <p class="text-xs text-gray-400 mt-1">Max 2MB</p>
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Save Settings</button>
    </form>
</div>
@endsection

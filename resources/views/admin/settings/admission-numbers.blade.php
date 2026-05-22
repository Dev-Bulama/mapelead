@extends('layouts.admin')
@section('title', 'Admission Number Settings')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Admission Number Settings</h1>
        <p class="text-gray-500 text-sm mt-1">Configure the auto-generated student admission number format</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Settings Form --}}
        <div class="bg-white rounded-xl border p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Format Configuration</h2>
            <form action="{{ route('admin.settings.admission-numbers.save') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prefix</label>
                    <input type="text" name="prefix" value="{{ old('prefix', $settings->prefix) }}" maxlength="10" required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="MAP">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Separator</label>
                    <select name="separator" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @foreach(['/' => 'Slash (/)', '-' => 'Dash (-)', '_' => 'Underscore (_)', '.' => 'Dot (.)'] as $val => $label)
                            <option value="{{ $val }}" {{ $settings->separator === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sequential Digit Length</label>
                    <select name="digit_length" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @for($i = 2; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ $settings->digit_length == $i ? 'selected' : '' }}>{{ $i }} digits ({{ str_pad(1, $i, '0', STR_PAD_LEFT) }})</option>
                        @endfor
                    </select>
                </div>

                <div class="mb-4 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="include_year" value="1" {{ $settings->include_year ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-brand-600">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Include Year</span>
                            <p class="text-xs text-gray-400">e.g. MAP/2026/0001</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="reset_yearly" value="1" {{ $settings->reset_yearly ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-brand-600">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Reset Counter Each Year</span>
                        </div>
                    </label>
                </div>

                <button type="submit" class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Save Settings</button>
            </form>
        </div>

        {{-- Preview & Actions --}}
        <div class="space-y-4">
            <div class="bg-brand-50 border border-brand-200 rounded-xl p-6">
                <h3 class="font-semibold text-brand-900 mb-2">Format Preview</h3>
                <div class="text-3xl font-mono font-bold text-brand-700 my-3">{{ $settings->previewFormat() }}</div>
                <p class="text-xs text-brand-600">Next number will be: <strong>{{ $settings->previewFormat() }}</strong></p>
                <p class="text-xs text-gray-500 mt-1">Last assigned: #{{ $settings->last_sequential_number }}</p>
            </div>

            <div class="bg-white rounded-xl border p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Bulk Assign</h3>
                <p class="text-sm text-gray-500 mb-3">Assign admission numbers to all students who don't have one yet.</p>
                <form action="{{ route('admin.settings.admission-numbers.bulk') }}" method="POST" onsubmit="return confirm('Bulk assign admission numbers to all students without one?')">
                    @csrf
                    <button type="submit" class="w-full bg-gray-900 text-white py-2 rounded-lg text-sm font-medium hover:bg-gray-800">Bulk Assign Numbers</button>
                </form>
            </div>

            <div class="bg-white rounded-xl border p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Assign to Specific User</h3>
                <form action="{{ route('admin.settings.admission-numbers.assign') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="number" name="user_id" placeholder="User ID" required
                        class="flex-1 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                    <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Assign</button>
                </form>
            </div>

            <a href="{{ route('admin.settings.admission-numbers.list') }}" class="block text-center text-brand-600 hover:underline text-sm py-2">
                View all assigned numbers →
            </a>
        </div>
    </div>
</div>
@endsection

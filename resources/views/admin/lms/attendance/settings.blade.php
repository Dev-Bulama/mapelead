@extends('layouts.admin')
@section('title', 'Attendance Settings')

@section('content')
<div class="p-6 max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.courses.attendance.index', $course) }}" class="text-brand-600 hover:underline text-sm">← Back to Attendance</a>
        <h1 class="text-xl font-bold text-gray-900 mt-2">Attendance Settings</h1>
        <p class="text-gray-500 text-sm">{{ $course->title }}</p>
    </div>

    @if(session('success'))<div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>@endif

    <div class="bg-white rounded-xl border p-6">
        <form action="{{ route('admin.courses.attendance.settings.save', $course) }}" method="POST">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Attendance % Required</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="minimum_percentage" min="0" max="100"
                        value="{{ old('minimum_percentage', $settings->minimum_percentage ?? 75) }}"
                        class="w-24 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                    <span class="text-gray-500 text-sm">% — required for certificate eligibility</span>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notify When Attendance Falls Below</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="notify_threshold" min="0" max="100"
                        value="{{ old('notify_threshold', $settings->notify_threshold ?? 80) }}"
                        class="w-24 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                    <span class="text-gray-500 text-sm">%</span>
                </div>
            </div>

            <div class="mb-5 space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="notify_student_below_threshold" value="1"
                        {{ ($settings->notify_student_below_threshold ?? true) ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-brand-600">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Notify Student</span>
                        <p class="text-xs text-gray-400">Send in-app notification when attendance drops below threshold</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="notify_admin_below_threshold" value="1"
                        {{ ($settings->notify_admin_below_threshold ?? false) ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-brand-600">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Notify Admin</span>
                        <p class="text-xs text-gray-400">Flag low-attendance students in admin dashboard</p>
                    </div>
                </label>
            </div>

            <button type="submit" class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Save Settings</button>
        </form>
    </div>
</div>
@endsection

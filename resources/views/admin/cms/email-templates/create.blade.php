@extends('layouts.admin')
@section('title', 'Create Email Template')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.email-templates.index') }}" class="text-brand-600 hover:underline text-sm">← Back to Templates</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Create Email Template</h1>
    </div>

    <form action="{{ route('admin.email-templates.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl border p-6 mb-4">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required
                        class="w-full border rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-brand-500"
                        placeholder="e.g. my_template_slug">
                    @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Body (HTML)</label>
                <textarea name="body_html" rows="16" required
                    class="w-full border rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-brand-500"
                    placeholder="Enter HTML content. Use {{student_name}}, {{course_name}}, {{balance}}, {{due_date}}, {{admission_number}}...">{{ old('body_html') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Create Template</button>
            <a href="{{ route('admin.email-templates.index') }}" class="border px-6 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection

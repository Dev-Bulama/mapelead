@extends('layouts.admin')
@section('title', 'Edit Email Template')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.email-templates.index') }}" class="text-brand-600 hover:underline text-sm">← Back to Templates</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ $emailTemplate->name }}</h1>
        </div>
        <a href="{{ route('admin.email-templates.preview', $emailTemplate) }}" target="_blank"
            class="border border-brand-600 text-brand-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-50">
            Preview
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.email-templates.update', $emailTemplate) }}" method="POST">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl border p-6 mb-4">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                    <input type="text" name="name" value="{{ old('name', $emailTemplate->name) }}" required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-gray-400 text-xs">(auto-generated, read-only)</span></label>
                    <input type="text" value="{{ $emailTemplate->slug }}" disabled
                        class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Subject</label>
                <input type="text" name="subject" value="{{ old('subject', $emailTemplate->subject) }}" required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                <p class="text-xs text-gray-400 mt-1">You can use placeholders like <code>{{student_name}}</code></p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Available Placeholders</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['student_name','balance','due_date','course_name','admission_number','amount_due'] as $var)
                    <button type="button" onclick="insertPlaceholder('{{{{ {$var} }}}}')"
                        class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono hover:bg-brand-50 hover:text-brand-700">
                        {{ '{{' . $var . '}}' }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Body (HTML)</label>
                <textarea id="body_html" name="body_html" rows="20" required
                    class="w-full border rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-brand-500">{{ old('body_html', $emailTemplate->body_html) }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Save Template</button>
            <a href="{{ route('admin.email-templates.index') }}" class="border px-6 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>

<script>
function insertPlaceholder(text) {
    const textarea = document.getElementById('body_html');
    const pos = textarea.selectionStart;
    textarea.value = textarea.value.substring(0, pos) + text + textarea.value.substring(textarea.selectionEnd);
    textarea.focus();
    textarea.setSelectionRange(pos + text.length, pos + text.length);
}
</script>
@endsection

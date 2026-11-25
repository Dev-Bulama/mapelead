@extends('layouts.admin')
@section('title', 'Edit Email Template')

@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="emailEditor()" x-init="init()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <a href="{{ route('admin.email-templates.index') }}" class="text-brand-600 hover:underline text-sm">← Back to Templates</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Edit: {{ $emailTemplate->name }}</h1>
        </div>
        <div class="flex gap-2">
            <button type="button" @click="togglePreview()"
                class="border border-brand-600 text-brand-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-50 transition">
                <span x-text="previewVisible ? 'Hide Preview' : 'Live Preview'"></span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.email-templates.update', $emailTemplate) }}" method="POST" id="templateForm">
        @csrf @method('PUT')

        {{-- Meta fields --}}
        <div class="bg-white rounded-xl border p-5 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                    <input type="text" name="name" value="{{ old('name', $emailTemplate->name) }}" required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-gray-400 text-xs">(read-only)</span></label>
                    <input type="text" value="{{ $emailTemplate->slug }}" disabled
                        class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Subject</label>
                <input type="text" name="subject" id="subjectInput"
                    value="{{ old('subject', $emailTemplate->subject) }}" required
                    @input="schedulePreview()"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                <p class="text-xs text-gray-400 mt-1">You can use placeholders like <code>{{student_name}}</code></p>
            </div>
        </div>

        {{-- Editor + Preview split pane --}}
        <div class="flex gap-4" :class="previewVisible ? 'flex-col lg:flex-row' : ''">

            {{-- Editor pane --}}
            <div :class="previewVisible ? 'lg:w-1/2' : 'w-full'" class="flex flex-col">

                {{-- Toolbar --}}
                <div class="bg-white border border-b-0 rounded-t-xl px-3 py-2 flex flex-wrap gap-1 items-center">
                    <span class="text-xs text-gray-400 mr-1">Insert:</span>

                    {{-- Variable chips --}}
                    @php
                        $vars = $emailTemplate->available_variables ?? ['student_name','balance','due_date','course_name','admission_number','amount_due'];
                    @endphp
                    @foreach($vars as $v)
                    <button type="button" @click="insertAtCursor('{{ '{{' . $v . '}}' }}')"
                        class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-mono hover:bg-blue-100 border border-blue-200">
                        {{ '{{' . $v . '}}' }}
                    </button>
                    @endforeach

                    <div class="w-px h-4 bg-gray-200 mx-1"></div>

                    {{-- Logo button --}}
                    @if(!empty($logo))
                    <button type="button" @click="insertAtCursor('<img src=\'{{ $logo }}\' alt=\'{{ $appName }}\' style=\'max-height:48px;\'>')"
                        class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs hover:bg-gray-200 border border-gray-200 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Logo
                    </button>
                    @endif

                    {{-- CTA button snippet --}}
                    <button type="button" @click="insertAtCursor('<a href=\'#\' style=\'display:inline-block;padding:12px 28px;background:#1e3adb;color:#fff;border-radius:8px;font-weight:600;text-decoration:none;font-size:14px;\'>Click Here</a>')"
                        class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs hover:bg-gray-200 border border-gray-200">
                        CTA Button
                    </button>

                    {{-- Divider --}}
                    <button type="button" @click="insertAtCursor('\n<hr style=\'border:none;border-top:1px solid #e5e7eb;margin:24px 0;\'>\n')"
                        class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs hover:bg-gray-200 border border-gray-200">
                        Divider
                    </button>

                    {{-- Starter template --}}
                    <button type="button" @click="loadStarter()"
                        class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded text-xs hover:bg-amber-100 border border-amber-200 ml-auto">
                        Load Starter
                    </button>
                </div>

                {{-- Textarea --}}
                <textarea id="body_html" name="body_html" rows="28" required
                    @input="schedulePreview()"
                    class="w-full border rounded-b-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-brand-500 resize-y"
                    style="min-height:420px;">{{ old('body_html', $emailTemplate->body_html) }}</textarea>
            </div>

            {{-- Live Preview pane --}}
            <div x-show="previewVisible" x-cloak :class="previewVisible ? 'lg:w-1/2' : ''" class="flex flex-col">
                <div class="bg-white border rounded-t-xl px-4 py-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Email Preview</span>
                    <span class="text-xs text-gray-400">Updates as you type</span>
                </div>
                <div class="border border-t-0 rounded-b-xl overflow-hidden" style="min-height:420px;">
                    <iframe id="previewFrame"
                        class="w-full"
                        style="height:520px;border:none;"
                        sandbox="allow-same-origin"></iframe>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 mt-4">
            <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Save Template</button>
            <a href="{{ route('admin.email-templates.index') }}" class="border px-6 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>

<script>
const LOGO_URL   = @json($logo ?? null);
const APP_NAME   = @json($appName ?? config('app.name'));

function emailEditor() {
    return {
        previewVisible: true,
        previewTimer: null,

        init() {
            this.$nextTick(() => this.renderPreview());
        },

        togglePreview() {
            this.previewVisible = !this.previewVisible;
            if (this.previewVisible) this.$nextTick(() => this.renderPreview());
        },

        schedulePreview() {
            clearTimeout(this.previewTimer);
            this.previewTimer = setTimeout(() => this.renderPreview(), 350);
        },

        renderPreview() {
            const body    = document.getElementById('body_html')?.value ?? '';
            const subject = document.getElementById('subjectInput')?.value ?? '';
            const html    = this.wrapEmail(body, subject);
            const frame   = document.getElementById('previewFrame');
            if (!frame) return;
            frame.srcdoc = html;
        },

        wrapEmail(body, subject) {
            const logoHtml = LOGO_URL
                ? `<img src="${LOGO_URL}" alt="${APP_NAME}" style="max-height:48px;width:auto;">`
                : `<div style="color:#fff;font-size:22px;font-weight:700;">${APP_NAME}</div>`;

            return `<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>${subject || APP_NAME}</title>
<style>
body{margin:0;padding:0;background:#f4f6fb;font-family:Helvetica,Arial,sans-serif;color:#1a1a2e;}
.wrapper{max-width:600px;margin:24px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);}
.header{background:#1e3adb;padding:24px 36px;text-align:center;}
.body-content{padding:32px 36px;font-size:15px;line-height:1.7;color:#374151;}
.body-content h1,.body-content h2,.body-content h3{color:#1a1a2e;}
.body-content a{color:#1e3adb;}
.footer{background:#f9fafb;padding:16px 36px;text-align:center;font-size:12px;color:#9ca3af;border-top:1px solid #e5e7eb;}
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">${logoHtml}</div>
  <div class="body-content">${body}</div>
  <div class="footer">&copy; ${new Date().getFullYear()} ${APP_NAME}. All rights reserved.</div>
</div>
</body>
</html>`;
        },

        insertAtCursor(text) {
            const ta  = document.getElementById('body_html');
            const pos = ta.selectionStart;
            ta.value  = ta.value.substring(0, pos) + text + ta.value.substring(ta.selectionEnd);
            ta.focus();
            ta.setSelectionRange(pos + text.length, pos + text.length);
            this.schedulePreview();
        },

        loadStarter() {
            const ta = document.getElementById('body_html');
            if (ta.value.trim() && !confirm('This will replace the current content. Continue?')) return;
            ta.value = `<p>Dear {{student_name}},</p>

<p>Thank you for your continued interest in <strong>${APP_NAME}</strong>.</p>

<p>Your enrollment for <strong>{{course_name}}</strong> has been confirmed. Your admission number is <strong>{{admission_number}}</strong>.</p>

<p>
  <a href="#" style="display:inline-block;padding:12px 28px;background:#1e3adb;color:#fff;border-radius:8px;font-weight:600;text-decoration:none;font-size:14px;">
    Access Your Course
  </a>
</p>

<hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">

<p style="font-size:13px;color:#6b7280;">If you have any questions, please reply to this email or contact our support team.</p>

<p>Warm regards,<br><strong>${APP_NAME} Team</strong></p>`;
            this.schedulePreview();
        },
    };
}
</script>
@endsection

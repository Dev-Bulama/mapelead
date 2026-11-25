@extends('layouts.admin')
@section('title', 'Announcements & News Ticker')
@section('content')
<div class="space-y-6" x-data="{ showForm: false }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Announcements</h2>
            <p class="text-sm text-gray-500 mt-1">Manage site-wide announcements, news tickers, and popups</p>
        </div>
        <button @click="showForm = !showForm"
            class="bg-brand-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Announcement
        </button>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Add Form --}}
    <div x-show="showForm" x-cloak class="bg-white rounded-2xl border p-6">
        <h3 class="font-semibold text-gray-900 border-b pb-3 mb-5">Create Announcement</h3>
        <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data"
              x-data="annForm()" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" x-model="type" required class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                        <option value="ticker">News Ticker (scrolling bar)</option>
                        <option value="bar">Banner Bar (fixed top)</option>
                        <option value="popup">Popup</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title (optional)</label>
                    <input type="text" name="title" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="e.g. New Batch Starting!">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span x-show="type !== 'popup'">Message <span class="text-red-500">*</span></span>
                        <span x-show="type === 'popup'">Short Message (shown as subtitle, optional)</span>
                    </label>
                    <textarea name="message" rows="2" :required="type !== 'popup'" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Announcement text..."></textarea>
                </div>
            </div>

            {{-- Popup-only: HTML body editor + media --}}
            <div x-show="type === 'popup'" class="border border-purple-200 rounded-xl bg-purple-50 p-4 space-y-4">
                <p class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Popup Rich Content</p>

                {{-- HTML Body editor --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">Popup HTML Body</label>
                        <div class="flex gap-1">
                            <button type="button" onclick="insertSnippet('new_body_html','bold')" class="px-2 py-0.5 bg-white border rounded text-xs font-bold text-gray-600 hover:bg-gray-50">B</button>
                            <button type="button" onclick="insertSnippet('new_body_html','italic')" class="px-2 py-0.5 bg-white border rounded text-xs text-gray-600 hover:bg-gray-50"><em>I</em></button>
                            <button type="button" onclick="insertSnippet('new_body_html','link')" class="px-2 py-0.5 bg-white border rounded text-xs text-gray-600 hover:bg-gray-50">Link</button>
                            <button type="button" onclick="insertSnippet('new_body_html','button')" class="px-2 py-0.5 bg-white border border-blue-200 text-blue-700 rounded text-xs hover:bg-blue-50">+ Button</button>
                            <button type="button" onclick="insertSnippet('new_body_html','divider')" class="px-2 py-0.5 bg-white border rounded text-xs text-gray-600 hover:bg-gray-50">— Divider</button>
                        </div>
                    </div>
                    <textarea id="new_body_html" name="body_html" rows="8"
                        class="w-full border rounded-xl px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-brand-500"
                        placeholder="<p>Write your popup HTML content here...</p>&#10;<p>You can use full HTML including <strong>bold</strong>, links, images, etc.</p>"></textarea>
                    <p class="text-xs text-gray-400 mt-1">Write full HTML. This is rendered inside the popup modal body.</p>
                </div>

                {{-- Media upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Media (optional)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" x-data="{ mediaChoice: 'none' }">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="media_type_choice" x-model="mediaChoice" value="none"> No Media
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="media_type_choice" x-model="mediaChoice" value="file"> Upload Image/Video
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="media_type_choice" x-model="mediaChoice" value="youtube"> YouTube/Video URL
                        </label>

                        <div x-show="mediaChoice === 'file'" x-cloak class="col-span-3">
                            <input type="file" name="media_file" accept="image/*,video/mp4,video/webm"
                                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                            <p class="text-xs text-gray-400 mt-1">Images: JPG, PNG, GIF, WebP (max 50MB). Videos: MP4, WebM (max 50MB).</p>
                        </div>

                        <div x-show="mediaChoice === 'youtube'" x-cloak class="col-span-3">
                            <input type="url" name="media_youtube" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                                placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/...">
                            <p class="text-xs text-gray-400 mt-1">Paste a YouTube or direct video URL.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Common fields --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                    <input type="url" name="url" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link Text</label>
                    <input type="text" name="url_text" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Learn More">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                    <input type="color" name="bg_color" value="#14215B" class="h-10 w-full border rounded-xl px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text Color</label>
                    <input type="color" name="text_color" value="#ffffff" class="h-10 w-full border rounded-xl px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="datetime-local" name="starts_at" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="datetime-local" name="ends_at" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-brand-600">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_dismissible" value="1" checked class="rounded text-brand-600">
                    <span class="text-sm text-gray-700">Dismissible</span>
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-brand-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">Create</button>
                <button type="button" @click="showForm = false" class="px-5 py-2 border rounded-xl text-sm text-gray-600 hover:bg-gray-50">Cancel</button>
            </div>
        </form>
    </div>

    {{-- Type guide --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm">
            <div class="font-semibold text-blue-700 mb-1">News Ticker</div>
            <p class="text-blue-600 text-xs">Scrolling text displayed on the homepage. Perfect for urgent announcements, upcoming batches, or course availability.</p>
        </div>
        <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 text-sm">
            <div class="font-semibold text-orange-700 mb-1">Banner Bar</div>
            <p class="text-orange-600 text-xs">Fixed bar displayed at the top of every page. Great for site-wide notices like maintenance windows or promotions.</p>
        </div>
        <div class="bg-purple-50 border border-purple-100 rounded-xl p-4 text-sm">
            <div class="font-semibold text-purple-700 mb-1">Popup</div>
            <p class="text-purple-600 text-xs">Modal dialog shown to visitors. Supports full HTML content, image flyers, and embedded videos.</p>
        </div>
    </div>

    {{-- Announcements List --}}
    @if($announcements->isEmpty())
    <div class="bg-white rounded-2xl border p-16 text-center text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
        <p class="text-sm font-medium">No announcements yet</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($announcements as $ann)
        <div class="bg-white rounded-2xl border p-5 flex items-start gap-4" x-data="{ editing: false, editType: '{{ $ann->type }}', editMedia: '{{ $ann->media_type ? ($ann->media_url ? ($ann->media_type === 'youtube' ? 'youtube' : 'keep') : 'none') : 'none' }}' }">
            <div class="w-2 h-12 rounded-full flex-shrink-0 mt-1" style="background-color: {{ $ann->bg_color ?? '#14215B' }}"></div>
            <div class="flex-1 min-w-0">
                <div x-show="!editing">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ $ann->type === 'ticker' ? 'bg-blue-100 text-blue-700' : ($ann->type === 'bar' ? 'bg-orange-100 text-orange-700' : 'bg-purple-100 text-purple-700') }}">
                            {{ ucfirst($ann->type) }}
                        </span>
                        @if(!$ann->is_active)
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                        @if($ann->ends_at && $ann->ends_at->isPast())
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Expired</span>
                        @endif
                        @if($ann->title)
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $ann->title }}</h3>
                        @endif
                    </div>
                    @if($ann->message)
                    <p class="text-sm text-gray-600 break-words">{{ $ann->message }}</p>
                    @endif
                    @if($ann->body_html)
                    <p class="text-xs text-purple-600 mt-1">✦ Has rich HTML content</p>
                    @endif
                    @if($ann->media_url)
                    <p class="text-xs text-indigo-600 mt-0.5">✦ Has media ({{ $ann->media_type }})</p>
                    @endif
                    @if($ann->url)
                    <a href="{{ $ann->url }}" target="_blank" class="text-xs text-brand-600 hover:underline mt-1 inline-block">{{ $ann->url_text ?? $ann->url }}</a>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">
                        Created {{ $ann->created_at->diffForHumans() }}
                        @if($ann->starts_at) · Starts {{ $ann->starts_at->format('d M Y') }} @endif
                        @if($ann->ends_at) · Ends {{ $ann->ends_at->format('d M Y') }} @endif
                    </p>
                </div>

                <div x-show="editing" x-cloak>
                    <form action="{{ route('admin.announcements.update', $ann) }}" method="POST"
                          enctype="multipart/form-data" class="space-y-3">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-2 gap-3">
                            <select name="type" x-model="editType" class="border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                <option value="ticker" {{ $ann->type === 'ticker' ? 'selected' : '' }}>News Ticker</option>
                                <option value="bar" {{ $ann->type === 'bar' ? 'selected' : '' }}>Banner Bar</option>
                                <option value="popup" {{ $ann->type === 'popup' ? 'selected' : '' }}>Popup</option>
                            </select>
                            <input type="text" name="title" value="{{ $ann->title }}" class="border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Title">
                        </div>

                        <textarea name="message" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Short message (required for bar/ticker)">{{ $ann->message }}</textarea>

                        {{-- Popup rich content --}}
                        <div x-show="editType === 'popup'" class="border border-purple-200 rounded-xl bg-purple-50 p-4 space-y-4">
                            <p class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Popup Rich Content</p>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-sm font-medium text-gray-700">HTML Body</label>
                                    <div class="flex gap-1">
                                        <button type="button" onclick="insertSnippet('edit_body_{{ $ann->id }}','bold')" class="px-2 py-0.5 bg-white border rounded text-xs font-bold text-gray-600 hover:bg-gray-50">B</button>
                                        <button type="button" onclick="insertSnippet('edit_body_{{ $ann->id }}','italic')" class="px-2 py-0.5 bg-white border rounded text-xs text-gray-600 hover:bg-gray-50"><em>I</em></button>
                                        <button type="button" onclick="insertSnippet('edit_body_{{ $ann->id }}','link')" class="px-2 py-0.5 bg-white border rounded text-xs text-gray-600 hover:bg-gray-50">Link</button>
                                        <button type="button" onclick="insertSnippet('edit_body_{{ $ann->id }}','button')" class="px-2 py-0.5 bg-white border border-blue-200 text-blue-700 rounded text-xs hover:bg-blue-50">+ Button</button>
                                        <button type="button" onclick="insertSnippet('edit_body_{{ $ann->id }}','divider')" class="px-2 py-0.5 bg-white border rounded text-xs text-gray-600 hover:bg-gray-50">— Divider</button>
                                    </div>
                                </div>
                                <textarea id="edit_body_{{ $ann->id }}" name="body_html" rows="8"
                                    class="w-full border rounded-xl px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-brand-500"
                                    placeholder="<p>Write your popup HTML content here...</p>">{{ $ann->body_html }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Media</label>
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="media_type" x-model="editMedia" value="none"> Remove media
                                    </label>
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="media_type" x-model="editMedia" value="keep"> Keep existing
                                    </label>
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="media_type" x-model="editMedia" value="file"> Upload new file
                                    </label>
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="media_type" x-model="editMedia" value="youtube"> YouTube URL
                                    </label>

                                    @if($ann->media_url)
                                    <div x-show="editMedia === 'keep'" class="col-span-4 text-xs text-gray-500">
                                        Current: <span class="font-medium">{{ $ann->media_type }}</span> —
                                        @if($ann->media_type === 'youtube')
                                            {{ Str::limit($ann->media_url, 60) }}
                                        @else
                                            <a href="{{ $ann->media_url }}" target="_blank" class="text-brand-600 underline">View file</a>
                                        @endif
                                    </div>
                                    @endif

                                    <div x-show="editMedia === 'file'" class="col-span-4">
                                        <input type="file" name="media_file" accept="image/*,video/mp4,video/webm"
                                            class="w-full border rounded-xl px-3 py-2 text-sm">
                                    </div>
                                    <div x-show="editMedia === 'youtube'" class="col-span-4">
                                        <input type="url" name="media_youtube" value="{{ $ann->media_type === 'youtube' ? $ann->media_url : '' }}"
                                            class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                                            placeholder="https://www.youtube.com/watch?v=...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <input type="url" name="url" value="{{ $ann->url }}" class="border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Link URL">
                            <input type="text" name="url_text" value="{{ $ann->url_text }}" class="border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Link Text">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-500">Starts</label>
                                <input type="datetime-local" name="starts_at" value="{{ $ann->starts_at ? $ann->starts_at->format('Y-m-d\TH:i') : '' }}" class="w-full border rounded-xl px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Ends</label>
                                <input type="datetime-local" name="ends_at" value="{{ $ann->ends_at ? $ann->ends_at->format('Y-m-d\TH:i') : '' }}" class="w-full border rounded-xl px-3 py-2 text-sm">
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-1.5 text-sm"><input type="checkbox" name="is_active" value="1" {{ $ann->is_active ? 'checked' : '' }} class="rounded text-brand-600"> Active</label>
                            <label class="flex items-center gap-1.5 text-sm"><input type="checkbox" name="is_dismissible" value="1" {{ $ann->is_dismissible ? 'checked' : '' }} class="rounded text-brand-600"> Dismissible</label>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-brand-700">Save</button>
                            <button type="button" @click="editing = false" class="border text-sm font-semibold px-4 py-2 rounded-xl hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            <div x-show="!editing" class="flex gap-2 flex-shrink-0">
                <button @click="editing = true" class="text-xs font-semibold text-gray-500 border rounded-lg px-3 py-1.5 hover:bg-gray-50 transition-colors">Edit</button>
                <form action="{{ route('admin.announcements.destroy', $ann) }}" method="POST" onsubmit="return confirm('Delete this announcement?')">
                    @csrf @method('DELETE')
                    <button class="text-xs font-semibold text-red-500 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50 transition-colors">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<script>
function annForm() {
    return { type: 'ticker' };
}

const POPUP_SNIPPETS = {
    bold:    '<strong>Bold text</strong>',
    italic:  '<em>Italic text</em>',
    link:    '<a href="#">Link Text</a>',
    button:  '<p style="text-align:center;"><a href="#" style="display:inline-block;padding:10px 24px;background:#1e3adb;color:#fff;border-radius:8px;font-weight:600;text-decoration:none;font-size:14px;">Click Here</a></p>',
    divider: '<hr style="border:none;border-top:1px solid #e5e7eb;margin:16px 0;">',
};

function insertSnippet(textareaId, key) {
    const ta = document.getElementById(textareaId);
    if (!ta) return;
    const snippet = POPUP_SNIPPETS[key] || '';
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    ta.value = ta.value.substring(0, start) + snippet + ta.value.substring(end);
    ta.focus();
    ta.setSelectionRange(start + snippet.length, start + snippet.length);
}
</script>
@endsection

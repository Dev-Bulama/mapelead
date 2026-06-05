@extends('layouts.admin')

@section('title', 'Media Library')

@section('content')
<div x-data="mediaUploader()" class="space-y-6">

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-2xl">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Media Library</h1>
            <p class="text-sm text-gray-500 mt-1">Manage uploaded files, images, and documents.</p>
        </div>
        <button @click="$refs.fileInput.click()"
            class="inline-flex items-center gap-2 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors shrink-0" style="background-color: #4f46e5;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
            Upload Files
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4">
        <div class="flex flex-col lg:flex-row gap-3">
            <form method="GET" action="{{ request()->url() }}" class="flex-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search files..."
                        class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    @if(request('folder')) <input type="hidden" name="folder" value="{{ request('folder') }}"> @endif
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-medium text-white px-3 py-1 rounded-lg transition-colors" style="background-color: #4f46e5;">Search</button>
                </div>
            </form>

            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl">
                    @foreach(['all' => 'All', 'images' => 'Images', 'documents' => 'Documents', 'videos' => 'Videos'] as $type => $label)
                    <a href="{{ request()->fullUrlWithQuery(['type' => $type === 'all' ? null : $type, 'search' => request('search'), 'folder' => request('folder')]) }}"
                        class="{{ request('type', 'all') === $type ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }} px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-150 whitespace-nowrap">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                @if(!empty($folders))
                <form method="GET" action="{{ request()->url() }}">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    <select name="folder" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-xl px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="">All Folders</option>
                        @foreach($folders as $folder)
                        <option value="{{ $folder }}" {{ request('folder') === $folder ? 'selected' : '' }}>{{ $folder }}</option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6"
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="handleDrop($event)"
        :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-dashed border-gray-300'"
        style="border-style: dashed; border-width: 2px;">

        <input type="file" name="files[]" multiple x-ref="fileInput"
            @change="setFiles([...$event.target.files])"
            class="hidden" accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.txt">

        <div class="text-center py-6" @click="$refs.fileInput.click()" :class="files.length === 0 ? 'cursor-pointer' : ''">
            <div class="mx-auto w-14 h-14 rounded-2xl flex items-center justify-center mb-3 transition-colors" :class="dragging ? 'bg-indigo-100' : 'bg-gray-100'">
                <svg class="w-7 h-7 transition-colors" :class="dragging ? 'text-indigo-500' : 'text-gray-400'" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-700" x-show="!dragging">Click to upload or drag and drop files here</p>
            <p class="text-sm font-semibold text-indigo-600" x-show="dragging">Drop files to upload</p>
            <p class="text-xs text-gray-400 mt-1">Images, PDFs, documents, videos supported</p>
        </div>

        <div x-show="files.length > 0" x-transition class="mt-4 border-t border-gray-100 pt-4">
            <p class="text-xs font-semibold text-gray-600 mb-3" x-text="files.length + ' file(s) selected'"></p>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                <template x-for="(file, index) in files" :key="index">
                    <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0 overflow-hidden">
                            <template x-if="file.type.startsWith('image/')">
                                <img :src="previewUrls[index]" class="w-full h-full object-cover rounded-lg" alt="">
                            </template>
                            <template x-if="!file.type.startsWith('image/')">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            </template>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate" x-text="file.name"></p>
                            <p class="text-xs text-gray-400" x-text="(file.size / 1024).toFixed(1) + ' KB'"></p>
                        </div>
                        <button type="button" @click="removeFile(index)" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>
            <div x-show="uploading" class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-500 h-2 rounded-full transition-all duration-300" :style="'width:' + uploadProgress + '%'"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1" x-text="'Uploading... ' + uploadProgress + '%'"></p>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="button" @click="uploadFiles()" :disabled="uploading"
                    class="inline-flex items-center gap-2 text-white text-sm font-medium px-5 py-2 rounded-xl transition-colors disabled:opacity-50"
                    style="background-color: #4f46e5;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                    <span x-show="!uploading">Upload <span x-text="files.length"></span> File(s)</span>
                    <span x-show="uploading">Uploading...</span>
                </button>
                <button type="button" @click="files = []; previewUrls = []" :disabled="uploading" class="bg-white border border-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors disabled:opacity-50">
                    Clear
                </button>
            </div>
        </div>
    </div>

    {{-- Newly uploaded thumbnails (appear immediately after AJAX upload) --}}
    <div x-show="newlyUploaded.length > 0" x-transition class="bg-white rounded-2xl border border-gray-200 p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Just Uploaded</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
            <template x-for="item in newlyUploaded" :key="item.id">
                <div class="relative border border-green-200 rounded-xl overflow-hidden bg-green-50 aspect-square">
                    <img :src="item.url" :alt="item.name" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-green-500/20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </template>
        </div>
    </div>

    @if($media->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="text-center py-16">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M2.25 12l5.15-5.15a2.25 2.25 0 013.182 0L18.75 12M2.25 19.5h19.5M3.75 19.5V7.125A1.875 1.875 0 015.625 5.25h12.75A1.875 1.875 0 0120.25 7.125V19.5"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 mb-1">No files yet</h3>
            <p class="text-sm text-gray-500 mb-4">Upload your first file using the upload zone above.</p>
            <button @click="$refs.fileInput.click()" class="inline-flex items-center gap-2 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors" style="background-color: #4f46e5;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                Upload Your First File
            </button>
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-5">
            <p class="text-sm text-gray-500">{{ $media->total() }} file{{ $media->total() !== 1 ? 's' : '' }}</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($media as $item)
            @php
                $isImage = str_starts_with($item->mime_type, 'image/');
                $isVideo = str_starts_with($item->mime_type, 'video/');
                $sizeKb = $item->size / 1024;
                $sizeFormatted = $sizeKb >= 1024
                    ? number_format($sizeKb / 1024, 1) . ' MB'
                    : number_format($sizeKb, 1) . ' KB';
            @endphp
            <div class="group relative border border-gray-200 rounded-xl overflow-hidden hover:border-indigo-300 hover:shadow-sm transition-all duration-150 bg-gray-50">
                <div class="relative">
                    <input type="checkbox" class="absolute top-2 left-2 z-10 w-4 h-4 text-indigo-600 border-gray-300 rounded opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity cursor-pointer">
                    @if($isImage)
                    <img src="{{ $item->url }}" alt="{{ $item->name }}" class="w-full h-28 object-cover">
                    @elseif($isVideo)
                    <div class="w-full h-28 bg-gray-900 flex items-center justify-center">
                        <svg class="w-10 h-10 text-white opacity-70" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    @else
                    <div class="w-full h-28 flex items-center justify-center">
                        @php
                            $ext = strtolower(pathinfo($item->file_name, PATHINFO_EXTENSION));
                            $extColors = ['pdf' => 'text-red-400', 'doc' => 'text-blue-400', 'docx' => 'text-blue-400', 'xls' => 'text-green-400', 'xlsx' => 'text-green-400', 'ppt' => 'text-orange-400', 'pptx' => 'text-orange-400', 'zip' => 'text-purple-400'];
                            $iconColor = $extColors[$ext] ?? 'text-gray-400';
                        @endphp
                        <div class="text-center">
                            <svg class="w-10 h-10 mx-auto {{ $iconColor }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            <span class="text-xs font-bold text-gray-500 uppercase mt-1 block">{{ $ext }}</span>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('admin.media.destroy', $item->id) }}" method="POST"
                        @click.prevent="if(confirm('Delete {{ addslashes($item->name) }}?')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="absolute top-2 right-2 z-10 w-7 h-7 bg-white rounded-lg shadow border border-gray-200 flex items-center justify-center text-red-500 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        </button>
                    </form>
                </div>

                <div class="p-2.5">
                    <p class="text-xs font-medium text-gray-800 truncate" title="{{ $item->name }}">{{ $item->name }}</p>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs text-gray-400">{{ $sizeFormatted }}</span>
                        <span class="text-xs text-gray-400">{{ $item->created_at->format('M d') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($media->hasPages())
        <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $media->firstItem() }}–{{ $media->lastItem() }} of {{ $media->total() }} files
            </p>
            <div class="flex items-center gap-1">
                @if($media->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-300 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed">Previous</span>
                @else
                <a href="{{ $media->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Previous</a>
                @endif

                @foreach($media->getUrlRange(max(1, $media->currentPage() - 2), min($media->lastPage(), $media->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}"
                    class="{{ $page == $media->currentPage() ? 'text-white' : 'text-gray-700 bg-white border border-gray-200 hover:bg-gray-50' }} px-3 py-1.5 text-sm font-medium rounded-lg transition-colors"
                    style="{{ $page == $media->currentPage() ? 'background-color: #4f46e5;' : '' }}">
                    {{ $page }}
                </a>
                @endforeach

                @if($media->hasMorePages())
                <a href="{{ $media->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Next</a>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-300 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

</div>

<script>
function mediaUploader() {
    return {
        files: [],
        previewUrls: [],
        dragging: false,
        uploading: false,
        uploadProgress: 0,
        newlyUploaded: [],

        handleDrop(e) {
            this.dragging = false;
            this.setFiles([...e.dataTransfer.files]);
        },

        setFiles(fileList) {
            this.files = fileList;
            this.previewUrls = [];
            fileList.forEach((f, i) => {
                if (f.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (ev) => { this.previewUrls[i] = ev.target.result; };
                    reader.readAsDataURL(f);
                } else {
                    this.previewUrls[i] = null;
                }
            });
        },

        removeFile(i) {
            this.files.splice(i, 1);
            this.previewUrls.splice(i, 1);
        },

        async uploadFiles() {
            if (!this.files.length) return;
            this.uploading = true;
            this.uploadProgress = 0;

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            this.files.forEach(f => formData.append('files[]', f));

            try {
                await new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                        }
                    });
                    xhr.addEventListener('load', () => {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            const data = JSON.parse(xhr.responseText);
                            if (data.success) {
                                this.newlyUploaded = [...this.newlyUploaded, ...data.files];
                                this.files = [];
                                this.previewUrls = [];
                                resolve(data);
                            } else {
                                reject(new Error('Upload failed'));
                            }
                        } else {
                            reject(new Error('Server error: ' + xhr.status));
                        }
                    });
                    xhr.addEventListener('error', () => reject(new Error('Network error')));
                    xhr.open('POST', '{{ route('admin.media.upload') }}');
                    xhr.send(formData);
                });
            } catch (err) {
                alert('Upload failed: ' + err.message);
            } finally {
                this.uploading = false;
                this.uploadProgress = 0;
            }
        }
    }
}
</script>
@endsection

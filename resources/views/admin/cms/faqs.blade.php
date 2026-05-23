@extends('layouts.admin')
@section('title', 'FAQs')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">FAQs</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage frequently asked questions.</p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm p-4">{{ session('success') }}</div>
    @endif

    {{-- Add Form --}}
    <div class="bg-white rounded-xl border p-5" x-data="{ open: false }">
        <button @click="open = !open"
                class="flex items-center gap-2 text-sm font-medium text-white px-4 py-2 rounded-lg hover:opacity-90 transition"
                style="background-color:#14215B;">
            <span x-text="open ? 'Cancel' : '+ Add FAQ'"></span>
        </button>

        <div x-show="open" x-transition class="mt-5 pt-5 border-t border-gray-100">
            <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Question <span class="text-red-500">*</span></label>
                    <input type="text" name="question" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                           placeholder="e.g. How do I enroll in a course?">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Answer <span class="text-red-500">*</span></label>
                    <textarea name="answer" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                              placeholder="Provide a clear, helpful answer..."></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                        <input type="text" name="category"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none"
                               placeholder="e.g. Billing, Enrollment">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                    </div>
                    <div class="flex items-end gap-4 pb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 rounded border-gray-300">
                            <span class="text-xs text-gray-600">Featured</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-gray-300">
                            <span class="text-xs text-gray-600">Active</span>
                        </label>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition"
                            style="background-color:#14215B;">
                        Add FAQ
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide w-8">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Question</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($faqs as $index => $faq)
                    <tr class="hover:bg-gray-50 transition-colors" x-data="{ editing: false }">
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <div x-show="!editing">
                                <p class="text-sm font-medium text-gray-900">{{ $faq->question }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ \Str::limit($faq->answer, 80) }}</p>
                            </div>
                            <div x-show="editing" x-transition>
                                <form action="{{ route('admin.faqs.update', $faq) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="question" value="{{ $faq->question }}" required
                                           class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                    <textarea name="answer" rows="3" required
                                              class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">{{ $faq->answer }}</textarea>
                                    <input type="text" name="category" value="{{ $faq->category }}"
                                           class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none"
                                           placeholder="Category">
                                    <div class="flex gap-3 items-center">
                                        <label class="flex items-center gap-1 text-xs text-gray-600">
                                            <input type="checkbox" name="is_active" value="1" @if($faq->is_active) checked @endif class="w-3.5 h-3.5">
                                            Active
                                        </label>
                                        <label class="flex items-center gap-1 text-xs text-gray-600">
                                            <input type="checkbox" name="is_featured" value="1" @if($faq->is_featured) checked @endif class="w-3.5 h-3.5">
                                            Featured
                                        </label>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit"
                                                class="text-xs font-medium text-white px-3 py-1.5 rounded transition hover:opacity-90"
                                                style="background-color:#14215B;">Save</button>
                                        <button type="button" @click="editing = false"
                                                class="text-xs font-medium text-gray-600 bg-gray-100 px-3 py-1.5 rounded hover:bg-gray-200 transition">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($faq->category)
                            <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $faq->category }}</span>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($faq->is_active)
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2" x-show="!editing">
                                <button @click="editing = true"
                                        class="text-xs font-medium px-3 py-1.5 rounded-lg text-white hover:opacity-90 transition"
                                        style="background-color:#14215B;">
                                    Edit
                                </button>
                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST"
                                      onsubmit="return confirm('Delete this FAQ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs font-medium bg-red-50 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100 transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">
                            No FAQs yet. Add your first one above.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

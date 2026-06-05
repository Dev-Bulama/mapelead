@extends('layouts.admin')
@section('title', 'Team Members')
@section('content')
<div class="space-y-6" x-data="{ showForm: false, editId: null }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Team Members</h2>
            <p class="text-sm text-gray-500 mt-1">Manage the team section displayed on the website</p>
        </div>
        <button @click="showForm = !showForm"
            class="bg-brand-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Member
        </button>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Add Form --}}
    <div x-show="showForm" x-cloak class="bg-white rounded-2xl border p-6 space-y-4">
        <h3 class="font-semibold text-gray-900 border-b pb-3">Add Team Member</h3>
        <form action="{{ route('admin.team.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Position / Title <span class="text-red-500">*</span></label>
                    <input type="text" name="position" required class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="e.g. Lead Instructor">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <input type="text" name="department" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="e.g. Cybersecurity">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="https://linkedin.com/in/...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Twitter URL</label>
                    <input type="url" name="twitter_url" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="https://twitter.com/...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                    <input type="file" name="photo" accept="image/*" class="w-full border rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" min="0" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea name="bio" rows="3" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Brief professional bio..."></textarea>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded text-brand-600">
                    <span class="text-sm text-gray-700">Featured</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-brand-600">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-brand-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">Add Member</button>
                <button type="button" @click="showForm = false" class="px-5 py-2 border rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-colors">Cancel</button>
            </div>
        </form>
    </div>

    {{-- Members Grid --}}
    @if($members->isEmpty())
    <div class="bg-white rounded-2xl border p-16 text-center text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <p class="text-sm font-medium">No team members yet</p>
        <p class="text-xs mt-1">Add team members to display them on the website.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($members as $member)
        <div class="bg-white rounded-2xl border overflow-hidden group" x-data="{ editing: false }">
            <div class="relative">
                <img src="{{ $member->photo_url }}" alt="{{ $member->name }}"
                    class="w-full h-48 object-cover">
                @if($member->is_featured)
                <span class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-0.5 rounded-full">Featured</span>
                @endif
                @if(!$member->is_active)
                <div class="absolute inset-0 bg-gray-900/40 flex items-center justify-center">
                    <span class="bg-gray-800 text-white text-xs font-semibold px-2 py-1 rounded-lg">Inactive</span>
                </div>
                @endif
            </div>
            <div class="p-4">
                <h3 class="font-bold text-gray-900 text-sm">{{ $member->name }}</h3>
                <p class="text-xs text-brand-600 font-medium mt-0.5">{{ $member->position }}</p>
                @if($member->department)
                <p class="text-xs text-gray-400">{{ $member->department }}</p>
                @endif
                @if($member->bio)
                <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $member->bio }}</p>
                @endif

                {{-- Edit Form --}}
                <div x-show="editing" x-cloak class="mt-3">
                    <form action="{{ route('admin.team.update', $member) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $member->name }}" class="w-full border rounded-lg px-2 py-1.5 text-xs focus:ring-1 focus:ring-brand-500" placeholder="Name">
                        <input type="text" name="position" value="{{ $member->position }}" class="w-full border rounded-lg px-2 py-1.5 text-xs focus:ring-1 focus:ring-brand-500" placeholder="Position">
                        <textarea name="bio" rows="2" class="w-full border rounded-lg px-2 py-1.5 text-xs focus:ring-1 focus:ring-brand-500" placeholder="Bio">{{ $member->bio }}</textarea>
                        <input type="file" name="photo" accept="image/*" class="w-full text-xs border rounded-lg px-2 py-1.5">
                        <div class="flex gap-2">
                            <label class="flex items-center gap-1 text-xs"><input type="checkbox" name="is_featured" value="1" {{ $member->is_featured ? 'checked' : '' }} class="rounded"> Featured</label>
                            <label class="flex items-center gap-1 text-xs"><input type="checkbox" name="is_active" value="1" {{ $member->is_active ? 'checked' : '' }} class="rounded"> Active</label>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-brand-600 text-white text-xs font-semibold py-1.5 rounded-lg hover:bg-brand-700">Save</button>
                            <button type="button" @click="editing = false" class="flex-1 border text-xs font-semibold py-1.5 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>

                <div x-show="!editing" class="flex gap-2 mt-3">
                    <button @click="editing = true" class="flex-1 text-xs font-semibold text-gray-600 border rounded-lg py-1.5 hover:bg-gray-50 transition-colors">Edit</button>
                    <form action="{{ route('admin.team.destroy', $member) }}" method="POST" onsubmit="return confirm('Delete {{ $member->name }}?')">
                        @csrf @method('DELETE')
                        <button class="px-3 text-xs font-semibold text-red-500 border border-red-200 rounded-lg py-1.5 hover:bg-red-50 transition-colors">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

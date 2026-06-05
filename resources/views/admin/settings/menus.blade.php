@extends('layouts.admin')
@section('title', 'Navigation Menus')
@section('content')
<div class="space-y-6" x-data="{ activeMenu: {{ $menus->first()?->id ?? 'null' }}, addingItem: false }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Navigation Menus</h2>
            <p class="text-sm text-gray-500 mt-1">Manage header, footer, and mobile navigation</p>
        </div>
        <button @click="addingItem = !addingItem"
            class="bg-brand-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Menu
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

    {{-- Create new menu --}}
    <div x-show="addingItem" x-cloak class="bg-white rounded-2xl border p-5">
        <h3 class="font-semibold text-gray-900 mb-4">Create New Menu</h3>
        <form action="{{ route('admin.settings.menus.store') }}" method="POST" class="flex gap-3 flex-wrap">
            @csrf
            <input type="text" name="name" placeholder="Menu name (e.g. Main Navigation)" required
                class="flex-1 min-w-48 border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            <input type="text" name="location" placeholder="Location slug (e.g. header, footer)" required
                class="flex-1 min-w-48 border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700">Create</button>
            <button type="button" @click="addingItem = false" class="px-4 py-2 border rounded-xl text-sm text-gray-600 hover:bg-gray-50">Cancel</button>
        </form>
    </div>

    @if($menus->isEmpty())
    <div class="bg-white rounded-2xl border p-16 text-center text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h7"/></svg>
        <p class="text-sm font-medium">No menus yet</p>
        <p class="text-xs mt-1">Create your first menu above or seed defaults from the admin terminal.</p>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Menu list (left sidebar) --}}
        <div class="space-y-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-1 mb-3">Menus</p>
            @foreach($menus as $menu)
            <button @click="activeMenu = {{ $menu->id }}"
                :class="activeMenu === {{ $menu->id }} ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-700 border-gray-200 hover:border-brand-300'"
                class="w-full border rounded-xl px-4 py-3 text-left text-sm font-medium transition-colors">
                <div class="font-semibold">{{ $menu->name }}</div>
                <div :class="activeMenu === {{ $menu->id }} ? 'text-brand-200' : 'text-gray-400'" class="text-xs mt-0.5">
                    {{ $menu->items->whereNull('parent_id')->count() }} items · <code>{{ $menu->location }}</code>
                </div>
            </button>
            @endforeach
        </div>

        {{-- Menu editor (right panel) --}}
        <div class="lg:col-span-3 space-y-4">

            @foreach($menus as $menu)
            <div x-show="activeMenu === {{ $menu->id }}" x-cloak x-data="{ addLink: false, addType: 'custom' }">

                {{-- Add item form --}}
                <div class="bg-white rounded-2xl border p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">{{ $menu->name }} <span class="text-gray-400 font-normal text-sm">— add item</span></h3>
                        <button @click="addLink = !addLink" class="text-xs font-semibold text-brand-600 hover:text-brand-700">
                            <span x-show="!addLink">+ Add Item</span>
                            <span x-show="addLink">− Hide Form</span>
                        </button>
                    </div>

                    <div x-show="addLink" x-cloak>
                        <div class="flex gap-2 mb-4">
                            <button @click="addType = 'custom'"
                                :class="addType === 'custom' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600'"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">Custom Link</button>
                            <button @click="addType = 'page'"
                                :class="addType === 'page' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600'"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">CMS Page</button>
                        </div>

                        <form action="{{ route('admin.settings.menus.items.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $menu->id }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Label <span class="text-red-500">*</span></label>
                                    <input type="text" name="label" required placeholder="e.g. About Us"
                                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                </div>

                                <div x-show="addType === 'custom'">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">URL <span class="text-red-500">*</span></label>
                                    <input type="text" name="url" placeholder="/about or https://..."
                                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                </div>

                                <div x-show="addType === 'page'">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Select Page <span class="text-red-500">*</span></label>
                                    <select name="page_id" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                        <option value="">-- Choose a page --</option>
                                        @foreach($pages as $page)
                                        <option value="{{ $page->id }}">{{ $page->title }} ({{ $page->slug }})</option>
                                        @endforeach
                                        @if($pages->isEmpty())
                                        <option disabled>No published pages yet</option>
                                        @endif
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Open In</label>
                                    <select name="target" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                        <option value="_self">Same tab</option>
                                        <option value="_blank">New tab</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Parent Item (optional)</label>
                                    <select name="parent_id" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                        <option value="">Top level</option>
                                        @foreach($menu->items->whereNull('parent_id') as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700">Add to Menu</button>
                                <button type="button" @click="addLink = false" class="px-4 py-2 border rounded-xl text-sm text-gray-600 hover:bg-gray-50">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Current items --}}
                <div class="bg-white rounded-2xl border overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 text-sm">Current Items</h3>
                        <span class="text-xs text-gray-400">Drag to reorder (or use sort fields)</span>
                    </div>

                    @php $topItems = $menu->items->whereNull('parent_id')->sortBy('sort_order'); @endphp

                    @if($topItems->isEmpty())
                    <div class="p-10 text-center text-gray-400 text-sm">No items yet. Add your first item above.</div>
                    @else
                    <div class="divide-y divide-gray-50" id="menu-{{ $menu->id }}-items">
                        @foreach($topItems as $item)
                        <div class="px-5 py-3" x-data="{ editing: false }">
                            <div x-show="!editing" class="flex items-start gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-medium text-gray-900 text-sm">{{ $item->label }}</span>
                                        @if(!$item->is_active)
                                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Hidden</span>
                                        @endif
                                        @if($item->target === '_blank')
                                        <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">External</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $item->url }}</p>

                                    {{-- Children --}}
                                    @foreach($item->children->sortBy('sort_order') as $child)
                                    <div class="ml-5 mt-2 border-l-2 border-gray-100 pl-3 flex items-center justify-between" x-data="{ editChild: false }">
                                        <div x-show="!editChild">
                                            <span class="text-xs font-medium text-gray-700">↳ {{ $child->label }}</span>
                                            <span class="text-xs text-gray-400 ml-2">{{ $child->url }}</span>
                                        </div>
                                        <div x-show="editChild" x-cloak>
                                            <form action="{{ route('admin.settings.menus.items.update', $child) }}" method="POST" class="flex gap-2 items-center">
                                                @csrf @method('PUT')
                                                <input type="text" name="label" value="{{ $child->label }}" class="border rounded-lg px-2 py-1 text-xs w-28 focus:ring-1 focus:ring-brand-500">
                                                <input type="text" name="url" value="{{ $child->url }}" class="border rounded-lg px-2 py-1 text-xs w-36 focus:ring-1 focus:ring-brand-500">
                                                <button type="submit" class="bg-brand-600 text-white text-xs px-2 py-1 rounded-lg">Save</button>
                                            </form>
                                        </div>
                                        <div class="flex gap-1 ml-2 shrink-0">
                                            <button @click="editChild = !editChild" class="text-xs text-gray-400 hover:text-brand-600">Edit</button>
                                            <form action="{{ route('admin.settings.menus.items.destroy', $child) }}" method="POST" onsubmit="return confirm('Remove this sub-item?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 ml-1">×</button>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button @click="editing = true" class="text-xs font-semibold text-gray-500 border rounded-lg px-2.5 py-1.5 hover:bg-gray-50">Edit</button>
                                    <form action="{{ route('admin.settings.menus.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove {{ addslashes($item->label) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-500 border border-red-200 rounded-lg px-2.5 py-1.5 hover:bg-red-50">Remove</button>
                                    </form>
                                </div>
                            </div>

                            {{-- Edit form --}}
                            <div x-show="editing" x-cloak>
                                <form action="{{ route('admin.settings.menus.items.update', $item) }}" method="POST" class="space-y-3">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Label</label>
                                            <input type="text" name="label" value="{{ $item->label }}"
                                                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">URL</label>
                                            <input type="text" name="url" value="{{ $item->url }}"
                                                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                                            <input type="number" name="sort_order" value="{{ $item->sort_order }}" min="0"
                                                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <select name="target" class="border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                            <option value="_self" {{ $item->target !== '_blank' ? 'selected' : '' }}>Same tab</option>
                                            <option value="_blank" {{ $item->target === '_blank' ? 'selected' : '' }}>New tab</option>
                                        </select>
                                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                                            <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }} class="rounded text-brand-600">
                                            Active
                                        </label>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-brand-700">Save</button>
                                        <button type="button" @click="editing = false" class="border text-sm font-semibold px-4 py-2 rounded-xl hover:bg-gray-50">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

            </div>
            @endforeach

            <div x-show="activeMenu === null" class="bg-white rounded-2xl border p-12 text-center text-gray-400 text-sm">
                Select a menu on the left to manage its items
            </div>

        </div>
    </div>
    @endif
</div>
@endsection

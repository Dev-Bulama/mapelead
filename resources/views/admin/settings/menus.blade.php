@extends('layouts.admin')
@section('title', 'Navigation Menus')

@section('content')
<div class="space-y-6" x-data="{ activeMenu: null }">

    <div>
        <h2 class="text-2xl font-bold text-gray-900">Navigation Menus</h2>
        <p class="text-sm text-gray-500 mt-1">Manage header navigation, footer columns, and mobile menus</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    @if($menus->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
            <p class="text-gray-500 mb-2">No navigation menus configured.</p>
            <p class="text-sm text-gray-400">Run <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">php artisan db:seed --class=NavigationSeeder</code> to set up default menus.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Menu List --}}
            <div class="space-y-3">
                @foreach($menus as $menu)
                    <button @click="activeMenu === {{ $menu->id }} ? activeMenu = null : activeMenu = {{ $menu->id }}"
                            class="w-full bg-white rounded-2xl border px-5 py-4 text-left transition-colors"
                            :class="activeMenu === {{ $menu->id }} ? 'border-brand-300 bg-brand-50' : 'border-gray-200 hover:border-gray-300'">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $menu->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $menu->items->count() }} items · {{ $menu->location }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="activeMenu === {{ $menu->id }} ? 'rotate-90' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </button>
                @endforeach
            </div>

            {{-- Menu Items Editor --}}
            <div class="lg:col-span-2">
                @foreach($menus as $menu)
                    <div x-show="activeMenu === {{ $menu->id }}" x-cloak class="bg-white rounded-2xl border border-gray-200">
                        <div class="p-5 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900">{{ $menu->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">Location: <code>{{ $menu->location }}</code></p>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @forelse($menu->items->where('parent_id', null)->sortBy('sort_order') as $item)
                                <div class="px-5 py-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $item->label }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $item->url }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($item->children->isNotEmpty())
                                                <span class="text-xs text-gray-400">{{ $item->children->count() }} children</span>
                                            @endif
                                            @if($item->target === '_blank')
                                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">External</span>
                                            @endif
                                        </div>
                                    </div>
                                    @foreach($item->children->sortBy('sort_order') as $child)
                                        <div class="ml-6 mt-2 pl-3 border-l-2 border-gray-100">
                                            <p class="text-xs font-medium text-gray-700">{{ $child->label }}</p>
                                            <p class="text-xs text-gray-400">{{ $child->url }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <div class="p-8 text-center text-sm text-gray-500">No items in this menu.</div>
                            @endforelse
                        </div>
                        <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                            <p class="text-xs text-gray-500">
                                Menu editing via admin panel coming soon. To manage menus programmatically, use the <code>NavigationMenu</code> and <code>NavigationItem</code> models or update them directly in the database.
                            </p>
                        </div>
                    </div>
                @endforeach

                @if(!$menus->isEmpty())
                    <div x-show="activeMenu === null" class="bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-400 text-sm">
                        Select a menu to view its items
                    </div>
                @endif
            </div>

        </div>
    @endif

</div>
@endsection

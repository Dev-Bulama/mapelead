@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="space-y-5">

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Users</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ number_format($users->total()) }} {{ Str::plural('user', $users->total()) }} registered
            </p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                  text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add User
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         FILTER BAR
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.users.index') }}"
              class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">

            {{-- Search --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search name or email…"
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg
                              focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>

            {{-- Role filter --}}
            <div class="shrink-0">
                <select name="role"
                        class="w-full sm:w-40 text-sm border border-gray-200 rounded-lg px-3 py-2
                               focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none
                               bg-white text-gray-700 transition">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status filter --}}
            <div class="shrink-0">
                <select name="status"
                        class="w-full sm:w-36 text-sm border border-gray-200 rounded-lg px-3 py-2
                               focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none
                               bg-white text-gray-700 transition">
                    <option value="">All Statuses</option>
                    @foreach(['active','inactive','pending','suspended'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter button --}}
            <button type="submit"
                    class="shrink-0 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium
                           px-4 py-2 rounded-lg transition-colors">
                Filter
            </button>

            {{-- Clear --}}
            @if(request()->hasAny(['search','role','status']))
                <a href="{{ route('admin.users.index') }}"
                   class="shrink-0 text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg
                          border border-gray-200 hover:border-gray-300 transition-colors">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         USERS TABLE
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="admin-table w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3.5 text-left font-medium">User</th>
                        <th class="px-4 py-3.5 text-left font-medium">Role</th>
                        <th class="px-4 py-3.5 text-center font-medium">Status</th>
                        <th class="px-4 py-3.5 text-left font-medium hidden lg:table-cell">Last Login</th>
                        <th class="px-4 py-3.5 text-left font-medium hidden md:table-cell">Joined</th>
                        <th class="px-5 py-3.5 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors" id="user-row-{{ $user->id }}">

                            {{-- Avatar + Name + Email --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/'.$user->avatar) }}"
                                             class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100 shrink-0"
                                             alt="">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-brand-100 flex items-center justify-center shrink-0 ring-2 ring-gray-100">
                                            <span class="text-brand-700 text-xs font-bold">
                                                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 truncate max-w-[180px]">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </p>
                                        <p class="text-xs text-gray-400 truncate max-w-[180px]">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Role badges --}}
                            <td class="px-4 py-3.5">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->getRoleNames() as $role)
                                        @php
                                            $roleColor = match($role) {
                                                'super_admin' => 'bg-red-100 text-red-700',
                                                'admin'       => 'bg-purple-100 text-purple-700',
                                                'instructor'  => 'bg-blue-100 text-blue-700',
                                                'student'     => 'bg-green-100 text-green-700',
                                                default       => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $roleColor }}">
                                            {{ ucwords(str_replace('_', ' ', $role)) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">No role</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Status badge (dynamic, updated via AJAX) --}}
                            <td class="px-4 py-3.5 text-center">
                                @php
                                    $sBadge = match($user->status) {
                                        'active'    => 'bg-green-100 text-green-700 ring-1 ring-green-200',
                                        'inactive'  => 'bg-gray-100 text-gray-500',
                                        'pending'   => 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200',
                                        'suspended' => 'bg-red-100 text-red-700 ring-1 ring-red-200',
                                        default     => 'bg-gray-100 text-gray-500',
                                    };
                                    $sDot = match($user->status) {
                                        'active'    => 'bg-green-500',
                                        'pending'   => 'bg-yellow-500',
                                        'suspended' => 'bg-red-500',
                                        default     => 'bg-gray-400',
                                    };
                                @endphp
                                <span id="status-badge-{{ $user->id }}"
                                      class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sBadge }}">
                                    <span id="status-dot-{{ $user->id }}" class="w-1.5 h-1.5 rounded-full {{ $sDot }}"></span>
                                    <span id="status-text-{{ $user->id }}">{{ ucfirst($user->status) }}</span>
                                </span>
                            </td>

                            {{-- Last Login --}}
                            <td class="px-4 py-3.5 text-xs text-gray-400 hidden lg:table-cell">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>

                            {{-- Joined --}}
                            <td class="px-4 py-3.5 text-xs text-gray-400 hidden md:table-cell">
                                {{ $user->created_at->format('M j, Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- View --}}
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                       class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="p-1.5 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Toggle Status (AJAX) --}}
                                    @if(!$user->hasRole('super_admin'))
                                        <button type="button"
                                                onclick="toggleUserStatus({{ $user->id }}, '{{ $user->status }}')"
                                                id="toggle-btn-{{ $user->id }}"
                                                class="p-1.5 rounded-lg transition-colors
                                                       {{ $user->status === 'active'
                                                            ? 'text-gray-400 hover:text-orange-600 hover:bg-orange-50'
                                                            : 'text-gray-400 hover:text-green-600 hover:bg-green-50' }}"
                                                title="{{ $user->status === 'active' ? 'Suspend user' : 'Activate user' }}">
                                            @if($user->status === 'active')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-sm font-medium">No users found</p>
                                    @if(request()->hasAny(['search','role','status']))
                                        <a href="{{ route('admin.users.index') }}"
                                           class="text-xs text-brand-600 hover:underline">Clear filters</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between gap-4">
                <p class="text-xs text-gray-400">
                    Showing
                    <span class="font-medium text-gray-600">{{ $users->firstItem() }}</span>
                    to
                    <span class="font-medium text-gray-600">{{ $users->lastItem() }}</span>
                    of
                    <span class="font-medium text-gray-600">{{ $users->total() }}</span>
                    results
                </p>
                <div>
                    {{ $users->appends(request()->query())->links('vendor.pagination.simple-tailwind') }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function toggleUserStatus(userId, currentStatus) {
        const btn = document.getElementById(`toggle-btn-${userId}`);
        if (btn) btn.disabled = true;

        try {
            const response = await fetch(`/admin/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) throw new Error('Server error');
            const data = await response.json();
            const newStatus = data.status;

            // Update badge text and classes
            const badge = document.getElementById(`status-badge-${userId}`);
            const dot   = document.getElementById(`status-dot-${userId}`);
            const text  = document.getElementById(`status-text-${userId}`);

            const badgeClasses = {
                active:    'bg-green-100 text-green-700 ring-1 ring-green-200',
                inactive:  'bg-gray-100 text-gray-500',
                pending:   'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200',
                suspended: 'bg-red-100 text-red-700 ring-1 ring-red-200',
            };
            const dotClasses = {
                active:    'bg-green-500',
                pending:   'bg-yellow-500',
                suspended: 'bg-red-500',
                inactive:  'bg-gray-400',
            };

            // Reset badge classes
            badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium '
                + (badgeClasses[newStatus] || 'bg-gray-100 text-gray-600');
            dot.className   = 'w-1.5 h-1.5 rounded-full ' + (dotClasses[newStatus] || 'bg-gray-400');
            text.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

            // Swap button icon
            if (btn) {
                const isNowActive = newStatus === 'active';
                btn.title = isNowActive ? 'Suspend user' : 'Activate user';
                btn.className = 'p-1.5 rounded-lg transition-colors '
                    + (isNowActive
                        ? 'text-gray-400 hover:text-orange-600 hover:bg-orange-50'
                        : 'text-gray-400 hover:text-green-600 hover:bg-green-50');
                btn.innerHTML = isNowActive
                    ? `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                       </svg>`
                    : `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                       </svg>`;
                btn.setAttribute('onclick', `toggleUserStatus(${userId}, '${newStatus}')`);
                btn.disabled = false;
            }

            // Brief flash feedback
            showToast(newStatus === 'active' ? 'User activated successfully.' : 'User suspended.', newStatus === 'active' ? 'success' : 'warning');

        } catch (err) {
            console.error(err);
            showToast('Failed to update user status. Please try again.', 'error');
            if (btn) btn.disabled = false;
        }
    }

    function showToast(message, type = 'success') {
        const colors = {
            success: 'bg-white border-green-200',
            warning: 'bg-white border-yellow-200',
            error:   'bg-white border-red-200',
        };
        const iconColors = {
            success: 'text-green-600',
            warning: 'text-yellow-600',
            error:   'text-red-600',
        };
        const icons = {
            success: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>`,
            warning: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>`,
            error:   `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>`,
        };

        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 max-w-sm flash-animate`;
        toast.innerHTML = `
            <div class="border rounded-xl shadow-lg p-4 flex items-start gap-3 ${colors[type] || colors.success}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-gray-50">
                    <svg class="w-5 h-5 ${iconColors[type] || ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${icons[type] || icons.success}
                    </svg>
                </div>
                <p class="text-sm text-gray-700 flex-1 mt-1">${message}</p>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 mt-0.5 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
</script>
@endpush

@extends('layouts.admin')
@section('title', 'Instructors')

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-2xl text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Instructors</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and monitor instructor performance.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Instructors', 'value' => $globalStats['total'],    'color' => 'bg-indigo-50 text-indigo-700'],
            ['label' => 'Verified',           'value' => $globalStats['verified'], 'color' => 'bg-green-50 text-green-700'],
            ['label' => 'Featured',           'value' => $globalStats['featured'], 'color' => 'bg-amber-50 text-amber-700'],
            ['label' => 'Session Slots',      'value' => $globalStats['sessions'], 'color' => 'bg-purple-50 text-purple-700'],
        ] as $stat)
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $stat['label'] }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3 font-medium text-gray-500 uppercase text-xs tracking-wide">Instructor</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500 uppercase text-xs tracking-wide">Courses</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500 uppercase text-xs tracking-wide">Students</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500 uppercase text-xs tracking-wide">Rating</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500 uppercase text-xs tracking-wide">Sessions</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500 uppercase text-xs tracking-wide">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500 uppercase text-xs tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($instructors as $instructor)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-indigo-100 shrink-0">
                                    @if($instructor->avatar_url)
                                    <img src="{{ $instructor->avatar_url }}" alt="{{ $instructor->full_name }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center text-indigo-600 font-bold text-sm">
                                        {{ strtoupper(substr($instructor->full_name, 0, 2)) }}
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $instructor->full_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $instructor->user?->email }}</p>
                                    @if($instructor->title)
                                    <p class="text-xs text-gray-500">{{ $instructor->title }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-700 font-medium">{{ $instructor->courses_count }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ number_format($instructor->total_students) }}</td>
                        <td class="px-5 py-4">
                            @if($instructor->average_rating > 0)
                            <span class="flex items-center gap-1 text-amber-500 font-semibold">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                {{ number_format($instructor->average_rating, 1) }}
                            </span>
                            @else
                            <span class="text-gray-400 text-xs">No ratings</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-gray-700 font-medium">{{ $instructor->session_assignments_count }}</span>
                            <span class="text-gray-400 text-xs ml-1">slots</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                @if($instructor->is_verified)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-50 text-green-700 text-xs font-medium rounded-full">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Verified
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>Unverified
                                </span>
                                @endif
                                @if($instructor->is_featured)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-medium rounded-full">
                                    ★ Featured
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.instructors.show', $instructor) }}"
                                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 px-2.5 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                                    View
                                </a>
                                <form action="{{ route('admin.instructors.toggle-verified', $instructor) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium {{ $instructor->is_verified ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }} px-2.5 py-1.5 rounded-lg transition-colors">
                                        {{ $instructor->is_verified ? 'Unverify' : 'Verify' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                            <p class="font-medium">No instructors found</p>
                            <p class="text-sm mt-1">Instructors will appear here once users are assigned the instructor role.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($instructors->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $instructors->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

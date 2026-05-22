@extends('layouts.student')

@section('title', 'My Students')
@section('page_title', 'My Students')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-gray-100 p-4">
        <form method="GET" action="{{ route('instructor.students') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="name" value="{{ request('name') }}"
                    placeholder="Search students by name..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
                Search
            </button>
            @if(request('name'))
            <a href="{{ route('instructor.students') }}" class="px-4 py-2.5 border border-gray-200 hover:border-gray-300 text-gray-600 hover:text-gray-800 text-sm font-medium rounded-xl transition-colors">
                Clear
            </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Enrolled Students</h3>
            <span class="text-sm text-gray-500">{{ $students->total() }} student{{ $students->total() !== 1 ? 's' : '' }}</span>
        </div>

        @if($students->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            @if(request('name'))
                <h3 class="mt-4 text-base font-semibold text-gray-900">No students found</h3>
                <p class="mt-1 text-sm text-gray-500">Try a different search term.</p>
            @else
                <h3 class="mt-4 text-base font-semibold text-gray-900">No students yet</h3>
                <p class="mt-1 text-sm text-gray-500">Students will appear here once they enrol in your courses.</p>
            @endif
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Student</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Course</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Enrolled</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Progress</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Status</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Last Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($students as $enrollment)
                    @php $user = $enrollment->user; $course = $enrollment->course; @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-2">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center overflow-hidden">
                                    @if($user && $user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-8 h-8 object-cover rounded-full">
                                    @else
                                        <span class="text-xs font-bold text-brand-600">
                                            {{ $user ? strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) : 'U' }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user ? ($user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))) : 'Unknown' }}</p>
                                    <p class="text-xs text-gray-400">{{ $user?->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-2 text-gray-700 max-w-[180px]">
                            <span class="truncate block">{{ $course?->title ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-2 text-gray-500 whitespace-nowrap">
                            {{ $enrollment->created_at->format('M d, Y') }}
                        </td>
                        <td class="py-3 px-2">
                            @php $progress = $enrollment->progress ?? 0; @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-20 bg-gray-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $progress >= 100 ? 'bg-green-500' : 'bg-brand-500' }}" style="width: {{ min($progress, 100) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-8">{{ $progress }}%</span>
                            </div>
                        </td>
                        <td class="py-3 px-2">
                            @if($enrollment->status === 'completed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Completed</span>
                            @elseif($enrollment->status === 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Active</span>
                            @elseif($enrollment->status === 'cancelled')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Cancelled</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 capitalize">{{ $enrollment->status ?? 'Enrolled' }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-gray-400 text-xs whitespace-nowrap">
                            @if($user && $user->last_login_at)
                                {{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $students->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

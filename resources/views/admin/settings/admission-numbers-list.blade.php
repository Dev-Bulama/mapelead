@extends('layouts.admin')
@section('title', 'Admission Numbers')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.settings.admission-numbers') }}" class="text-brand-600 hover:underline text-sm">← Settings</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Assigned Admission Numbers</h1>
        </div>
        <span class="text-sm text-gray-500">{{ $users->total() }} total</span>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Admission No.</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Student</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Email</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-bold text-brand-700">{{ $user->admission_number }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ $user->avatar_url }}" class="w-7 h-7 rounded-full object-cover">
                            <span class="font-medium">{{ $user->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($user->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">No admission numbers assigned yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="p-4 border-t">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection

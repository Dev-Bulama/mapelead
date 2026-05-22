@extends('layouts.admin')
@section('title', 'Certificates')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Certificates</h1>
            <p class="text-gray-500 text-sm mt-1">Issue and manage student certificates</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.certificates.settings') }}" class="border border-brand-600 text-brand-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-50">Certificate Settings</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Issue Certificate Form --}}
    <div class="bg-white rounded-xl border p-5 mb-6">
        <h2 class="font-semibold text-gray-900 mb-3">Issue New Certificate</h2>
        <form action="{{ route('admin.certificates.issue') }}" method="POST" class="flex gap-3 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Enrollment ID</label>
                <input type="number" name="enrollment_id" placeholder="Enter enrollment ID" required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            </div>
            <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 whitespace-nowrap">Issue Certificate</button>
        </form>
    </div>

    {{-- Certificates Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Certificate No.</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Student</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Course</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Attendance</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Quiz Avg</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Issued</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($certificates as $cert)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-brand-700">{{ $cert->certificate_number }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $cert->user->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $cert->user->admission_number }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $cert->course->title }}</td>
                    <td class="px-4 py-3">{{ $cert->attendance_percentage ?? 'N/A' }}%</td>
                    <td class="px-4 py-3">{{ $cert->quiz_score_average ?? 'N/A' }}%</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $cert->issued_at?->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            @if($cert->verification_token)
                            <a href="{{ route('certificate.verify', $cert->verification_token) }}" target="_blank" class="text-brand-600 hover:underline text-xs">Verify</a>
                            @endif
                            <form action="{{ route('admin.certificates.revoke', $cert) }}" method="POST" onsubmit="return confirm('Revoke this certificate?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Revoke</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No certificates issued yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($certificates->hasPages())
        <div class="p-4 border-t">{{ $certificates->links() }}</div>
        @endif
    </div>
</div>
@endsection

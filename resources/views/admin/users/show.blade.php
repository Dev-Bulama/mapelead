@extends('layouts.admin')
@section('title', 'User: ' . $user->full_name)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-brand-100">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $user->full_name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    @foreach($user->roles as $role)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-100 text-brand-700">
                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                        </span>
                    @endforeach
                    @php
                        $statusColors = ['active'=>'bg-green-100 text-green-700','suspended'=>'bg-red-100 text-red-700','inactive'=>'bg-gray-100 text-gray-600','pending'=>'bg-yellow-100 text-yellow-700'];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$user->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.edit', $user->id) }}"
               class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                Edit User
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="border border-gray-200 text-gray-600 hover:text-gray-900 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                ← Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-4">
                <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">Contact Info</h3>
                @foreach([
                    ['label' => 'Email', 'value' => $user->email],
                    ['label' => 'Phone', 'value' => $user->phone ?? '—'],
                    ['label' => 'Country', 'value' => $user->country ?? '—'],
                    ['label' => 'Joined', 'value' => $user->created_at->format('M d, Y')],
                    ['label' => 'Last Login', 'value' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never'],
                    ['label' => 'Last IP', 'value' => $user->last_login_ip ?? '—'],
                ] as $row)
                    <div class="flex justify-between items-start text-sm">
                        <span class="text-gray-500 font-medium">{{ $row['label'] }}</span>
                        <span class="text-gray-900 text-right max-w-[60%] truncate">{{ $row['value'] }}</span>
                    </div>
                @endforeach
            </div>

            @if($user->bio)
                <div class="bg-white rounded-2xl border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide mb-3">Bio</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $user->bio }}</p>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide mb-3">Quick Actions</h3>
                <div class="space-y-2">
                    <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-3 py-2.5 rounded-xl text-sm font-medium {{ $user->status === 'active' ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }} transition-colors">
                            {{ $user->status === 'active' ? 'Suspend Account' : 'Activate Account' }}
                        </button>
                    </form>
                    @if(!$user->isSuperAdmin())
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                              onsubmit="return confirm('Delete this user permanently? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full text-left px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                Delete User
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Course Enrollments ({{ $user->enrollments->count() }})</h3>
                </div>
                @if($user->enrollments->isEmpty())
                    <div class="p-8 text-center text-gray-500 text-sm">No enrollments yet.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Course</th>
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Progress</th>
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Status</th>
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Enrolled</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($user->enrollments->take(5) as $enrollment)
                                    <tr>
                                        <td class="px-5 py-3 font-medium text-gray-900 max-w-xs truncate">{{ $enrollment->course->title ?? 'N/A' }}</td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-24 bg-gray-100 rounded-full h-1.5">
                                                    <div class="bg-brand-600 rounded-full h-1.5" style="width: {{ $enrollment->progress_percent }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $enrollment->progress_percent }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $enrollment->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-gray-500">{{ $enrollment->enrolled_at?->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Payment History ({{ $user->payments->count() }})</h3>
                </div>
                @if($user->payments->isEmpty())
                    <div class="p-8 text-center text-gray-500 text-sm">No payment records.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Reference</th>
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Amount</th>
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Gateway</th>
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Status</th>
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($user->payments->take(5) as $payment)
                                    <tr>
                                        <td class="px-5 py-3 font-mono text-xs text-brand-600">{{ $payment->reference }}</td>
                                        <td class="px-5 py-3 font-semibold">₦{{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-5 py-3 capitalize text-gray-600">{{ $payment->gateway }}</td>
                                        <td class="px-5 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $payment->status === 'success' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-gray-500">{{ $payment->paid_at?->format('M d, Y') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

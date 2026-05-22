@extends('layouts.student')
@section('title', $assignment->title)

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    {{-- Breadcrumb --}}
    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <a href="{{ route('student.dashboard') }}" class="hover:text-brand-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('student.courses') }}" class="hover:text-brand-600">My Courses</a>
        <span>/</span>
        <span class="text-gray-600">Assignment</span>
    </nav>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Assignment Info --}}
    <div class="bg-white rounded-xl border p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $assignment->title }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $assignment->description }}</p>
            </div>
            @if($mySubmission && $mySubmission->status === 'graded')
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $mySubmission->passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $mySubmission->passed ? 'Passed' : 'Not Passed' }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-4 border-y mb-4">
            <div class="text-center">
                <p class="text-xs text-gray-400">Max Score</p>
                <p class="text-lg font-bold text-gray-900">{{ $assignment->max_score }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400">Pass Score</p>
                <p class="text-lg font-bold text-gray-900">{{ $assignment->pass_score }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400">Max Attempts</p>
                <p class="text-lg font-bold text-gray-900">{{ $assignment->max_attempts }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400">Due Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $assignment->due_date?->format('M d, Y') ?? 'No deadline' }}</p>
            </div>
        </div>

        @if($assignment->instructions)
        <div class="prose prose-sm max-w-none text-gray-700">
            {!! $assignment->instructions !!}
        </div>
        @endif
    </div>

    {{-- My Latest Submission --}}
    @if($mySubmission)
    <div class="bg-white rounded-xl border p-6 mb-6">
        <h2 class="font-semibold text-gray-900 mb-3">My Submission</h2>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Status</span>
                @php $statusColors = ['submitted'=>'blue','graded'=>'green','returned'=>'yellow','resubmit'=>'orange','pending'=>'gray']; $sc = $statusColors[$mySubmission->status] ?? 'gray'; @endphp
                <span class="px-2 py-0.5 rounded-full text-xs bg-{{ $sc }}-100 text-{{ $sc }}-700">{{ ucfirst($mySubmission->status) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Submitted</span>
                <span>{{ $mySubmission->submitted_at?->format('M d, Y H:i') }}</span>
            </div>
            @if($mySubmission->score !== null)
            <div class="flex justify-between">
                <span class="text-gray-500">Score</span>
                <span class="font-bold text-lg">{{ $mySubmission->score }} / {{ $assignment->max_score }}</span>
            </div>
            @endif
            @if($mySubmission->feedback)
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 mt-2">
                <p class="text-xs font-medium text-blue-700 mb-1">Instructor Feedback</p>
                <p class="text-blue-900 text-sm">{{ $mySubmission->feedback }}</p>
            </div>
            @endif
            @if($mySubmission->file_path)
            <div class="flex justify-between items-center">
                <span class="text-gray-500">Submitted File</span>
                <a href="{{ Storage::url($mySubmission->file_path) }}" class="text-brand-600 hover:underline text-xs" target="_blank">
                    {{ $mySubmission->original_filename ?? 'Download' }}
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Submit Form --}}
    @php
        $submissionCount = \App\Models\AssignmentSubmission::where('assignment_id', $assignment->id)->where('user_id', auth()->id())->count();
        $canSubmit = $submissionCount < $assignment->max_attempts;
    @endphp

    @if($canSubmit)
    <div class="bg-white rounded-xl border p-6">
        <h2 class="font-semibold text-gray-900 mb-4">Submit Assignment</h2>
        <p class="text-xs text-gray-400 mb-4">Attempts used: {{ $submissionCount }} / {{ $assignment->max_attempts }}</p>

        <form action="{{ route('student.assignment.submit', $assignment) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes / Answer</label>
                <textarea name="notes" rows="5" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Write your answer or notes here...">{{ old('notes') }}</textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload File (optional)</label>
                <input type="file" name="file" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                <p class="text-xs text-gray-400 mt-1">Max 20MB. Accepted: {{ $assignment->allowed_file_types ?? 'All file types' }}</p>
            </div>
            <button type="submit" class="w-full bg-brand-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-brand-700 transition">Submit Assignment</button>
        </form>
    </div>
    @else
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center">
        <p class="text-gray-500">You have reached the maximum number of submissions ({{ $assignment->max_attempts }}).</p>
    </div>
    @endif
</div>
@endsection

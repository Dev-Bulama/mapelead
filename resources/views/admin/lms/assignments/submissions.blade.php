@extends('layouts.admin')
@section('title', 'Assignment Submissions')

@section('content')
<div class="p-6" x-data="{ gradeModal: false, currentSubmission: null }">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Submissions: {{ $assignment->title }}</h1>
            <p class="text-gray-500 text-sm">Max: {{ $assignment->max_score }} pts | Pass: {{ $assignment->pass_score }} pts</p>
        </div>
    </div>

    @if(session('success'))<div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>@endif

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Student</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Submitted</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">File</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Score</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($submissions as $submission)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $submission->user->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $submission->user->admission_number ?? $submission->user->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $submission->submitted_at?->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-3">
                        @if($submission->file_path)
                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="text-brand-600 hover:underline text-xs">
                            {{ Str::limit($submission->original_filename ?? 'File', 20) }}
                        </a>
                        @else
                        <span class="text-gray-400 text-xs">No file</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-bold text-gray-900">{{ $submission->score !== null ? $submission->score . '/' . $assignment->max_score : '—' }}</td>
                    <td class="px-4 py-3">
                        @php $sc = ['submitted'=>'blue','graded'=>'green','returned'=>'yellow','pending'=>'gray','resubmit'=>'orange']; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs bg-{{ $sc[$submission->status] ?? 'gray' }}-100 text-{{ $sc[$submission->status] ?? 'gray' }}-700">{{ ucfirst($submission->status) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <button
                            @click="currentSubmission = {{ json_encode(['id' => $submission->id, 'student' => $submission->user->full_name, 'max' => $assignment->max_score, 'notes' => $submission->notes]) }}; gradeModal = true"
                            class="text-brand-600 hover:underline text-xs">
                            Grade
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No submissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($submissions->hasPages())<div class="p-4 border-t">{{ $submissions->links() }}</div>@endif
    </div>

    {{-- Grade Modal --}}
    <div x-show="gradeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="gradeModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <h3 class="font-bold text-gray-900 mb-1">Grade Submission</h3>
            <p class="text-sm text-gray-500 mb-4" x-text="currentSubmission?.student"></p>

            <div class="mb-4 bg-gray-50 rounded-lg p-3 text-sm text-gray-700 max-h-24 overflow-y-auto" x-show="currentSubmission?.notes">
                <p class="text-xs font-medium text-gray-400 mb-1">Student Notes</p>
                <p x-text="currentSubmission?.notes"></p>
            </div>

            <template x-if="currentSubmission">
                <form :action="'{{ url('admin/submissions') }}/' + currentSubmission.id + '/grade'" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Score</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="score" min="0" :max="currentSubmission.max" required
                                class="w-24 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                            <span class="text-gray-500 text-sm">/ <span x-text="currentSubmission.max"></span> pts</span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Feedback (optional)</label>
                        <textarea name="feedback" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Write feedback for the student..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-brand-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Submit Grade</button>
                        <button type="button" @click="gradeModal = false" class="flex-1 border py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection

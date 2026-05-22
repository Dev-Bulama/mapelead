@extends('layouts.admin')
@section('title', 'Preview: ' . $emailTemplate->name)

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" class="text-brand-600 hover:underline text-sm">← Back to Edit</a>
            <h1 class="text-xl font-bold text-gray-900 mt-2">Preview: {{ $emailTemplate->name }}</h1>
        </div>
        <span class="text-xs text-gray-400 bg-yellow-50 border border-yellow-200 px-3 py-1 rounded-full">Using sample data</span>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden mb-4">
        <div class="bg-gray-50 border-b px-5 py-3">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium mb-1">Subject</p>
            <p class="text-gray-900 font-medium">{{ $preview['subject'] }}</p>
        </div>
        <div class="p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium mb-3">Body Preview</p>
            <div class="border rounded-lg overflow-hidden bg-gray-50">
                <iframe srcdoc="{{ htmlspecialchars($preview['body']) }}" class="w-full" style="height:600px;border:0;" title="Email Preview"></iframe>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-5">
        <h3 class="font-semibold text-gray-900 mb-3 text-sm">Sample Variables Used</h3>
        <div class="grid grid-cols-2 gap-2 text-sm">
            @foreach(['student_name' => 'John Doe', 'course_name' => 'Sample Course', 'balance' => '50,000', 'due_date' => now()->addDays(7)->format('M d, Y'), 'admission_number' => 'MAP/2026/0001', 'amount_due' => '25,000'] as $key => $val)
            <div class="flex gap-2">
                <code class="text-xs bg-gray-100 px-2 py-0.5 rounded text-brand-700">{{ '{{' . $key . '}}' }}</code>
                <span class="text-gray-500">→ {{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

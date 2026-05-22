@extends('layouts.student')
@section('title', 'My Certificates')
@section('page_title', 'Certificates')

@section('content')
<div class="space-y-6">
    @if($certificates->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <div class="w-20 h-20 bg-yellow-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">No certificates yet</h3>
            <p class="text-gray-500 mt-2">Complete a course to earn your certificate.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($certificates as $cert)
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    {{-- Certificate Preview --}}
                    <div class="bg-gradient-to-br from-brand-950 to-purple-900 p-8 text-center relative">
                        <div class="absolute inset-0 opacity-10" style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23fff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")"></div>
                        <div class="relative">
                            <svg class="w-12 h-12 text-yellow-400 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                            <p class="text-yellow-300 text-xs font-medium tracking-widest uppercase mb-2">Certificate of Completion</p>
                            <p class="text-white font-display font-bold text-lg">{{ $cert->course->title }}</p>
                            <p class="text-brand-300 text-sm mt-2">{{ $cert->certificate_number }}</p>
                            <p class="text-brand-400 text-xs mt-1">Issued {{ $cert->issued_at->format('F j, Y') }}</p>
                        </div>
                    </div>
                    <div class="p-4 flex gap-2">
                        <a href="{{ route('student.certificate.download', $cert->id) }}"
                           class="flex-1 text-center bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
                            Download PDF
                        </a>
                        <button onclick="navigator.clipboard.writeText('{{ url('/verify-certificate/' . $cert->certificate_number) }}')"
                                class="px-4 py-2.5 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-sm text-gray-600">
                            Share
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Services\LMS\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly CertificateService $certificateService) {}

    public function index(Request $request): JsonResponse
    {
        $certificates = Certificate::where('user_id', $request->user()->id)
            ->with(['course:id,title,slug,thumbnail'])
            ->orderByDesc('issued_at')
            ->get();

        return $this->success(
            $certificates->map(fn($c) => $this->formatCertificate($c))->values()
        );
    }

    public function show(Request $request, int $certificateId): JsonResponse
    {
        $certificate = Certificate::where('id', $certificateId)
            ->where('user_id', $request->user()->id)
            ->with(['course', 'user'])
            ->first();

        if (!$certificate) {
            return $this->error('Certificate not found', 404);
        }

        return $this->success($this->formatCertificate($certificate, detailed: true));
    }

    public function verify(string $token): JsonResponse
    {
        $certificate = $this->certificateService->findByToken($token);

        if (!$certificate) {
            return $this->error('Certificate not found or invalid verification token', 404);
        }

        return $this->success([
            'valid'              => true,
            'certificate_number' => $certificate->certificate_number,
            'issued_at'          => $certificate->issued_at?->toDateString(),
            'expires_at'         => $certificate->expires_at?->toDateString(),
            'holder'             => [
                'name'  => $certificate->user?->full_name,
                'email' => $certificate->user?->email,
            ],
            'course'             => [
                'title' => $certificate->course?->title,
            ],
        ]);
    }

    public function checkEligibility(Request $request, int $courseId): JsonResponse
    {
        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment) {
            return $this->error('Enrollment not found', 404);
        }

        $result = $this->certificateService->isEligible($enrollment);

        return $this->success([
            'eligible' => $result['eligible'],
            'reasons'  => $result['reasons'] ?? [],
        ]);
    }

    public function claim(Request $request, int $courseId): JsonResponse
    {
        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment) {
            return $this->error('Enrollment not found', 404);
        }

        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();

        if ($existing) {
            return $this->success($this->formatCertificate($existing, detailed: true), message: 'Certificate already issued');
        }

        $result = $this->certificateService->isEligible($enrollment);

        if (!$result['eligible']) {
            return $this->error(
                'Not eligible: ' . implode(', ', $result['reasons'] ?? ['Complete all lessons first']),
                422
            );
        }

        $certificate = $this->certificateService->issue($enrollment);

        return $this->success($this->formatCertificate($certificate, detailed: true), message: 'Certificate issued successfully');
    }

    private function formatCertificate(Certificate $certificate, bool $detailed = false): array
    {
        $data = [
            'id'                 => $certificate->id,
            'certificate_number' => $certificate->certificate_number,
            'issued_at'          => $certificate->issued_at?->toDateString(),
            'expires_at'         => $certificate->expires_at?->toDateString(),
            'course'             => $certificate->course ? [
                'id'            => $certificate->course->id,
                'title'         => $certificate->course->title,
                'slug'          => $certificate->course->slug,
                'thumbnail_url' => $certificate->course->thumbnail_url ?? null,
            ] : null,
        ];

        if ($detailed) {
            $data['file_url']     = $certificate->file_path
                ? asset('storage/' . $certificate->file_path)
                : null;
            $data['verification_url'] = url('/certificate/verify/' . $certificate->certificate_number);
            $data['holder']       = $certificate->user ? [
                'name'  => $certificate->user->full_name,
                'email' => $certificate->user->email,
            ] : null;
        }

        return $data;
    }
}

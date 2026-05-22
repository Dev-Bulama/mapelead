<?php

namespace App\Http\Controllers\Admin;

use App\Models\Certificate;
use App\Models\CertificateSetting;
use App\Models\Enrollment;
use App\Services\LMS\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateSettingsController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $settings     = CertificateSetting::instance();
        $certificates = Certificate::with(['user', 'course'])->paginate(20);

        return view('admin.lms.certificates.index', compact('settings', 'certificates'));
    }

    public function settings()
    {
        $settings = CertificateSetting::instance();

        return view('admin.lms.certificates.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'organization_name' => 'nullable',
            'header_text'       => 'nullable',
            'body_text'         => 'nullable',
            'footer_text'       => 'nullable',
            'signatory_name'    => 'nullable',
            'signatory_title'   => 'nullable',
            'background_color'  => 'nullable',
            'primary_color'     => 'nullable',
            'text_color'        => 'nullable',
            'layout'            => 'nullable|in:landscape,portrait',
            'qr_enabled'        => 'boolean',
        ]);

        $settings = CertificateSetting::instance();

        foreach (['logo_path', 'signature_path', 'seal_path'] as $field) {
            if ($request->hasFile($field)) {
                $request->validate([
                    $field => 'image|max:2048',
                ]);

                $path = $request->file($field)->store('certificates/assets', 'public');
                $validated[$field] = $path;
            }
        }

        $settings->update($validated);

        return redirect()->back()->with('success', 'Certificate settings saved successfully.');
    }

    public function issue(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required',
        ]);

        $enrollment = Enrollment::with(['user', 'course'])->findOrFail($request->enrollment_id);

        $service       = app(CertificateService::class);
        $eligibility   = $service->checkEligibility($enrollment);

        if (! $eligibility['eligible']) {
            $reasons = implode(' ', $eligibility['reasons'] ?? []);

            return redirect()->back()->with('error', "Not eligible: {$reasons}");
        }

        $service->issue($enrollment);

        return redirect()->back()->with('success', "Certificate issued for {$enrollment->user->full_name}.");
    }

    public function revoke(Certificate $certificate)
    {
        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return redirect()->back()->with('success', 'Certificate revoked successfully.');
    }

    /**
     * Public certificate verification (routed via web, not admin middleware).
     */
    public function verify(string $token)
    {
        $certificate = CertificateService::findByToken($token);

        if (! $certificate) {
            return view('web.certificate-verify', ['notFound' => true]);
        }

        return view('web.certificate-verify', compact('certificate'));
    }
}
